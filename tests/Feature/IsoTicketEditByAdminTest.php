<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\IsoTicket;
use App\Models\IsoTicketDocument;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentStatusUpdated;
use App\Mail\TicketStatusChanged;

class IsoTicketEditByAdminTest extends TestCase
{
    use DatabaseTransactions;

    protected User $idcAdmin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $suffix = uniqid();
        $baseId = (int) (\App\Models\User::max('id') ?? 50000) + mt_rand(5000, 10000);

        $this->idcAdmin = new User();
        $this->idcAdmin->id = $baseId + 1;
        $this->idcAdmin->email = "idcadmin_{$suffix}@test.com";
        $this->idcAdmin->password = bcrypt('secret');
        $this->idcAdmin->role = 'IDC Admin';
        $this->idcAdmin->save();

        $this->regularUser = new User();
        $this->regularUser->id = $baseId + 2;
        $this->regularUser->email = "employee_{$suffix}@test.com";
        $this->regularUser->password = bcrypt('secret');
        $this->regularUser->role = 'Employee';
        $this->regularUser->save();
    }

    public function test_unauthorized_user_cannot_update_ticket_details()
    {
        $ticket = IsoTicket::create([
            'originating_section' => 'Original Section',
            'sharepoint_link'     => 'https://sharepoint.com/folder1',
            'message_to_idc'      => 'Original message to IDC',
            'status'              => 'submitted_to_idc',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-TEST-001',
            'is_registered'       => false,
        ]);

        $response = $this->actingAs($this->regularUser)
            ->putJson("/iso/idc/tickets/{$ticket->id}/update-details", [
                'originating_section' => 'Hacked Section',
                'documents' => [
                    [
                        'document_code'  => 'HAU-001',
                        'document_title' => 'Title 1',
                        'classification' => 'addition',
                        'source_type'    => 'forms',
                    ]
                ]
            ]);

        $response->assertStatus(403);
    }

    public function test_cannot_update_registered_ticket()
    {
        $ticket = IsoTicket::create([
            'originating_section' => 'Original Section',
            'sharepoint_link'     => 'https://sharepoint.com/folder1',
            'message_to_idc'      => 'Original message to IDC',
            'status'              => 'approved',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-TEST-002',
            'is_registered'       => true,
        ]);

        $response = $this->actingAs($this->idcAdmin)
            ->putJson("/iso/idc/tickets/{$ticket->id}/update-details", [
                'originating_section' => 'Updated Section',
                'documents' => [
                    [
                        'document_code'  => 'HAU-001',
                        'document_title' => 'Title 1',
                        'classification' => 'addition',
                        'source_type'    => 'forms',
                    ]
                ]
            ]);

        $response->assertStatus(403);
        $this->assertStringContainsString('Cannot modify a registered ticket', $response->json('message'));
    }

    public function test_idc_admin_can_update_ticket_and_document_details()
    {
        $ticket = IsoTicket::create([
            'originating_section' => 'Old Section Name',
            'sharepoint_link'     => 'https://sharepoint.com/folder1',
            'message_to_idc'      => 'Original message to IDC',
            'status'              => 'submitted_to_idc',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-TEST-003',
            'is_registered'       => false,
        ]);

        $doc = IsoTicketDocument::create([
            'ticket_id'      => $ticket->id,
            'document_code'  => 'OLD-CODE-001',
            'document_title' => 'Old Document Title',
            'classification' => 'addition',
            'source_type'    => 'forms',
            'status'         => 'submitted_to_idc',
        ]);

        $response = $this->actingAs($this->idcAdmin)
            ->putJson("/iso/idc/tickets/{$ticket->id}/update-details", [
                'originating_section' => 'Corrected Section Name',
                'documents' => [
                    [
                        'id'             => $doc->id,
                        'document_code'  => 'NEW-CODE-999',
                        'document_title' => 'Corrected Document Title',
                        'classification' => 'revision',
                        'source_type'    => 'procedures',
                        'specific_type'  => 'SOP',
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $ticket->refresh();
        $this->assertEquals('Corrected Section Name', $ticket->originating_section);
        // Ensure SharePoint and message remain untouched
        $this->assertEquals('https://sharepoint.com/folder1', $ticket->sharepoint_link);
        $this->assertEquals('Original message to IDC', $ticket->message_to_idc);

        $doc->refresh();
        $this->assertEquals('NEW-CODE-999', $doc->document_code);
        $this->assertEquals('Corrected Document Title', $doc->document_title);
        $this->assertEquals('revision', $doc->classification);
        $this->assertEquals('procedures', $doc->source_type);
        $this->assertEquals('SOP', $doc->specific_type);
    }

    public function test_idc_admin_can_remove_bulk_document_from_ticket()
    {
        $ticket = IsoTicket::create([
            'originating_section' => 'Bulk Section',
            'sharepoint_link'     => 'https://sharepoint.com/folder2',
            'message_to_idc'      => 'Bulk submission',
            'status'              => 'submitted_to_idc',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-TEST-004',
            'is_registered'       => false,
        ]);

        $doc1 = IsoTicketDocument::create([
            'ticket_id'      => $ticket->id,
            'document_code'  => 'DOC-KEEP-01',
            'document_title' => 'Document to keep',
            'classification' => 'addition',
            'source_type'    => 'forms',
            'status'         => 'submitted_to_idc',
        ]);

        $doc2 = IsoTicketDocument::create([
            'ticket_id'      => $ticket->id,
            'document_code'  => 'DOC-REMOVE-02',
            'document_title' => 'Wrong Document submitted in bulk',
            'classification' => 'addition',
            'source_type'    => 'forms',
            'status'         => 'submitted_to_idc',
        ]);

        // Retain only doc1
        $response = $this->actingAs($this->idcAdmin)
            ->putJson("/iso/idc/tickets/{$ticket->id}/update-details", [
                'originating_section' => 'Bulk Section',
                'documents' => [
                    [
                        'id'             => $doc1->id,
                        'document_code'  => 'DOC-KEEP-01',
                        'document_title' => 'Document to keep',
                        'classification' => 'addition',
                        'source_type'    => 'forms',
                    ]
                ]
            ]);

        $response->assertStatus(200);

        // doc1 still exists
        $this->assertDatabaseHas('iso_ticket_documents', [
            'id' => $doc1->id,
        ]);

        // doc2 was deleted
        $this->assertDatabaseMissing('iso_ticket_documents', [
            'id' => $doc2->id,
        ]);
    }

    public function test_validation_requires_at_least_one_document()
    {
        $ticket = IsoTicket::create([
            'originating_section' => 'Test Section',
            'sharepoint_link'     => 'https://sharepoint.com/folder3',
            'message_to_idc'      => 'Test',
            'status'              => 'submitted_to_idc',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-TEST-005',
            'is_registered'       => false,
        ]);

        $response = $this->actingAs($this->idcAdmin)
            ->putJson("/iso/idc/tickets/{$ticket->id}/update-details", [
                'originating_section' => 'Test Section',
                'documents'           => [],
            ]);

        $response->assertStatus(422);
    }

    public function test_validation_requires_code_and_title()
    {
        $ticket = IsoTicket::create([
            'originating_section' => 'Test Section',
            'sharepoint_link'     => 'https://sharepoint.com/folder4',
            'message_to_idc'      => 'Test',
            'status'              => 'submitted_to_idc',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-TEST-006',
            'is_registered'       => false,
        ]);

        $response = $this->actingAs($this->idcAdmin)
            ->putJson("/iso/idc/tickets/{$ticket->id}/update-details", [
                'originating_section' => 'Test Section',
                'documents' => [
                    [
                        'document_code'  => '',
                        'document_title' => 'Valid Title',
                        'classification' => 'addition',
                    ]
                ],
            ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('requires a Document Code', $response->json('message'));
    }

    public function test_revision_with_updated_title_preserves_original_title_on_registration()
    {
        // 1. Create original master document in registry (Rev 0)
        $originalMaster = \App\Models\IsoMasterDocument::create([
            'document_code'       => 'TEST-REV-001',
            'document_title'      => 'Original Document Title',
            'source_type'         => 'forms',
            'specific_type'       => null,
            'originating_section' => 'CSDO',
            'current_revision'    => 0,
            'is_original'         => 1,
            'original_document_id'=> null,
            'status'              => 'Active',
            'registered_at'       => now()->subDays(10),
            'source'              => 'ticket',
        ]);

        // 2. Create ticket revising this document with an updated title
        $ticket = IsoTicket::create([
            'originating_section' => 'CSDO',
            'sharepoint_link'     => 'https://sharepoint.com/folder5',
            'message_to_idc'      => 'Revising document with updated title',
            'status'              => 'approved',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-TEST-007',
            'is_registered'       => false,
        ]);

        $ticketDoc = IsoTicketDocument::create([
            'ticket_id'                   => $ticket->id,
            'document_code'               => 'TEST-REV-001',
            'document_title'              => 'Updated Document Title for Revision 1',
            'classification'              => 'revision',
            'source_type'                 => 'forms',
            'specific_type'               => null,
            'status'                      => 'approved',
            'registered_at'               => now(),
            'revising_master_document_id' => $originalMaster->id,
        ]);

        // 3. Register the ticket as IDC Admin
        $response = $this->actingAs($this->idcAdmin)
            ->patch("/iso/idc/{$ticket->id}/register");

        $response->assertRedirect(route('iso.idc.dashboard'));

        // 4. Verify original master document is Superseded, and its title has NOT been overwritten
        $originalMaster->refresh();
        $this->assertEquals('Superseded', $originalMaster->status);
        $this->assertEquals('Original Document Title', $originalMaster->document_title);
        $this->assertEquals(0, $originalMaster->current_revision);

        // 5. Verify new revised master document has the updated title, revision 1, status Active
        $revisedMaster = \App\Models\IsoMasterDocument::where('document_code', 'TEST-REV-001')
            ->where('current_revision', 1)
            ->first();

        $this->assertNotNull($revisedMaster);
        $this->assertEquals('Updated Document Title for Revision 1', $revisedMaster->document_title);
        $this->assertEquals('Active', $revisedMaster->status);
        $this->assertEquals(false, (bool) $revisedMaster->is_original);
        $this->assertEquals($originalMaster->id, $revisedMaster->original_document_id);
    }

    public function test_idc_admin_can_stage_document_title_in_management_module()
    {
        $doc = \App\Models\IsoMasterDocument::create([
            'document_code'       => 'TEST-STAGE-001',
            'document_title'      => 'Original CSDO Policy',
            'source_type'         => 'forms',
            'specific_type'       => null,
            'originating_section' => 'CSDO',
            'current_revision'    => 0,
            'is_original'         => 1,
            'original_document_id'=> null,
            'status'              => 'Active',
            'registered_at'       => now()->subDays(5),
            'source'              => 'ticket',
        ]);

        // 1. IDC Admin accesses the Document Titles management page
        $response = $this->actingAs($this->idcAdmin)
            ->get(route('iso.management.titles.index'));
        $response->assertStatus(200);

        // 2. IDC Admin updates the title for revision
        $updateResponse = $this->actingAs($this->idcAdmin)
            ->put("/iso/management/document-titles/{$doc->id}", [
                'revised_document_title' => 'CSDO Staged New Title Form',
            ]);
        $updateResponse->assertRedirect();

        $doc->refresh();
        $this->assertEquals('CSDO Staged New Title Form', $doc->revised_document_title);
        // Original title is NOT changed yet
        $this->assertEquals('Original CSDO Policy', $doc->document_title);

        // 3. Document Handler fetches documents for CSDO
        $fetchResponse = $this->actingAs($this->regularUser)
            ->getJson('/iso/documents/by-office?office=CSDO');
        $fetchResponse->assertStatus(200);
        $found = collect($fetchResponse->json())->firstWhere('id', $doc->id);
        $this->assertNotNull($found);
        $this->assertEquals('CSDO Staged New Title Form', $found['revised_document_title']);

        // 4. Clear staged title test
        $clearResponse = $this->actingAs($this->idcAdmin)
            ->delete("/iso/management/document-titles/{$doc->id}/clear");
        $clearResponse->assertRedirect();
        $doc->refresh();
        $this->assertNull($doc->revised_document_title);
    }

    public function test_identical_staged_title_clears_revision_and_inactive_documents_are_rejected()
    {
        $doc = \App\Models\IsoMasterDocument::create([
            'document_code'       => 'TEST-STAGE-002',
            'document_title'      => 'Active Document Title',
            'source_type'         => 'forms',
            'specific_type'       => null,
            'originating_section' => 'CSDO',
            'current_revision'    => 0,
            'is_original'         => 1,
            'original_document_id'=> null,
            'status'              => 'Active',
            'registered_at'       => now()->subDays(5),
            'source'              => 'ticket',
        ]);

        // Staging the identical title should clear staged title
        $response = $this->actingAs($this->idcAdmin)
            ->putJson("/iso/management/document-titles/{$doc->id}", [
                'revised_document_title' => 'Active Document Title',
            ]);
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $doc->refresh();
        $this->assertNull($doc->revised_document_title);

        // Inactive documents cannot be updated
        $doc->update(['status' => 'Superseded']);
        $inactiveResponse = $this->actingAs($this->idcAdmin)
            ->putJson("/iso/management/document-titles/{$doc->id}", [
                'revised_document_title' => 'New Title Attempt',
            ]);
        $inactiveResponse->assertStatus(422);
    }

    public function test_non_admin_cannot_override_revision_title_and_deletions_use_original_title()
    {
        $masterDoc = \App\Models\IsoMasterDocument::create([
            'document_code'       => 'TEST-SEC-001',
            'document_title'      => 'True Master Title',
            'revised_document_title' => 'IDC Approved Staged Title',
            'source_type'         => 'forms',
            'specific_type'       => null,
            'originating_section' => 'CSDO',
            'current_revision'    => 0,
            'is_original'         => 1,
            'original_document_id'=> null,
            'status'              => 'Active',
            'registered_at'       => now()->subDays(10),
            'source'              => 'ticket',
        ]);

        // Non-admin attempts to submit a revision ticket with a tampered title
        $revisionDocs = [
            [
                'code' => 'TEST-SEC-001',
                'title' => 'Tampered Employee Title',
                'classification' => 'revision',
                'source' => 'forms',
                'specificType' => null,
                'revisingMasterId' => $masterDoc->id,
            ]
        ];

        $response = $this->actingAs($this->regularUser)->post(route('iso.document.store'), [
            'originating_section' => 'CSDO',
            'sharepoint_link' => 'https://sharepoint.com/folder',
            'message_to_idc' => 'Revision submission',
            'documents' => json_encode($revisionDocs),
        ]);
        $response->assertRedirect(route('iso.document'));

        $ticketDoc = \App\Models\IsoTicketDocument::where('document_code', 'TEST-SEC-001')->first();
        $this->assertNotNull($ticketDoc);
        // The title should be the IDC Approved Staged Title, NOT the tampered title!
        $this->assertEquals('IDC Approved Staged Title', $ticketDoc->document_title);

        // Clean up ticket doc
        $ticketDoc->ticket->delete();

        // Non-admin attempts to submit a deletion ticket (deletion must always use original master title)
        $deletionDocs = [
            [
                'code' => 'TEST-SEC-001',
                'title' => 'Whatever Title Was In Request',
                'classification' => 'deletion',
                'source' => 'forms',
                'specificType' => null,
                'revisingMasterId' => $masterDoc->id,
            ]
        ];

        $delResponse = $this->actingAs($this->regularUser)->post(route('iso.document.store'), [
            'originating_section' => 'CSDO',
            'sharepoint_link' => 'https://sharepoint.com/folder',
            'message_to_idc' => 'Deletion submission',
            'documents' => json_encode($deletionDocs),
        ]);
        $delResponse->assertRedirect(route('iso.document'));

        $delTicketDoc = \App\Models\IsoTicketDocument::where('document_code', 'TEST-SEC-001')
            ->where('classification', 'deletion')
            ->first();
        $this->assertNotNull($delTicketDoc);
        // Deletions MUST use the master document's true title
        $this->assertEquals('True Master Title', $delTicketDoc->document_title);
    }

    public function test_bulk_submission_partial_approval_notifies_user_without_premature_ticket_approval()
    {
        Mail::fake();

        // 1. Create a bulk ticket with 3 documents
        $ticket = IsoTicket::create([
            'originating_section' => 'Registrar',
            'sharepoint_link'     => 'https://sharepoint.com/bulk-test',
            'message_to_idc'      => 'Bulk submission of 3 documents',
            'status'              => 'submitted_to_idc',
            'created_by'          => $this->regularUser->id,
            'ticket_number'       => 'TCK-BULK-001',
            'is_registered'       => false,
        ]);

        $doc1 = IsoTicketDocument::create([
            'ticket_id'      => $ticket->id,
            'document_code'  => 'BULK-001',
            'document_title' => 'Procedure A',
            'classification' => 'addition',
            'source_type'    => 'procedures',
            'status'         => 'submitted_to_idc',
        ]);

        $doc2 = IsoTicketDocument::create([
            'ticket_id'      => $ticket->id,
            'document_code'  => 'BULK-002',
            'document_title' => 'Procedure B',
            'classification' => 'addition',
            'source_type'    => 'procedures',
            'status'         => 'submitted_to_idc',
        ]);

        $doc3 = IsoTicketDocument::create([
            'ticket_id'      => $ticket->id,
            'document_code'  => 'BULK-003',
            'document_title' => 'Procedure C',
            'classification' => 'addition',
            'source_type'    => 'procedures',
            'status'         => 'submitted_to_idc',
        ]);

        // 2. Admin approves Document 1 only
        $res1 = $this->actingAs($this->idcAdmin)->patchJson("/iso/idc/{$doc1->id}/status", [
            'status' => 'approved',
        ]);
        $res1->assertOk();

        // Document 1 should be approved, but the Ticket itself must NOT be approved prematurely
        $doc1->refresh();
        $ticket->refresh();
        $this->assertEquals('approved', $doc1->status);
        $this->assertEquals('submitted_to_idc', $ticket->status);

        // A partial notification must be sent for doc1, and the overall ticket completion email must NOT be sent
        Mail::assertSent(DocumentStatusUpdated::class, function ($mail) use ($doc1, $ticket) {
            return $mail->document->id === $doc1->id
                && $mail->ticket->id === $ticket->id
                && $mail->approvedCount === 1
                && $mail->totalCount === 3
                && $mail->hasTo($this->regularUser->email);
        });
        Mail::assertNotSent(TicketStatusChanged::class);

        // 3. Admin places Document 2 On-Hold
        $res2 = $this->actingAs($this->idcAdmin)->patchJson("/iso/idc/{$doc2->id}/status", [
            'status' => 'on_hold',
        ]);
        $res2->assertOk();

        $doc2->refresh();
        $ticket->refresh();
        $this->assertEquals('on_hold', $doc2->status);
        $this->assertEquals('on_hold', $ticket->status);

        // Should notify about doc2 being placed on-hold
        Mail::assertSent(DocumentStatusUpdated::class, function ($mail) use ($doc2) {
            return $mail->document->id === $doc2->id
                && $mail->document->status === 'on_hold';
        });

        // 4. Admin resolves Doc 2 and approves it
        $this->actingAs($this->idcAdmin)->patchJson("/iso/idc/{$doc2->id}/status", [
            'status' => 'approved',
        ]);

        // 5. Admin approves Doc 3 (All 3 documents now approved!)
        $this->actingAs($this->idcAdmin)->patchJson("/iso/idc/{$doc3->id}/status", [
            'status' => 'approved',
        ]);

        $ticket->refresh();
        $this->assertEquals('approved', $ticket->status);

        // Now that ALL documents are approved, the final TicketStatusChanged notification must be sent
        Mail::assertSent(TicketStatusChanged::class, function ($mail) use ($ticket) {
            return $mail->ticket->id === $ticket->id
                && $mail->ticket->status === 'approved'
                && $mail->hasTo($this->regularUser->email);
        });
    }
}

