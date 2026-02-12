<?php

namespace App\Imports;

use App\Models\IsoMasterDocument;
use App\Helpers\IsoManagement\OfficeMapper;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class DocumentsImport implements ToCollection, WithHeadingRow
{
    protected $imported = 0;
    protected $errors = [];
    protected $importedDocumentCodes = []; // Track what's imported in each session.

    public function collection(Collection $rows){
        // Filter out empty rows
        $rows = $rows->filter(function($row){
           return !empty($row['document_code']); 
        });

        // Import all ACTIVE originals first (even if may deleted counterpart)
        foreach ($rows as $index => $row){
            $rowNumber = $index + 2; //+2 becuase: 0-indexed + header row

            if ((int)$row['current_revision'] === 0 && $row['status'] !== 'Deleted'){
                try{
                    $this->importRow($row, $rowNumber);
                    $this->imported++;
                    // Track the imported code
                    $this->importedDocumentCodes[] = $row['document_code'];
                } catch (\Exception $e){
                    $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
        }

        // Import all ACTIVE revisions
        foreach ($rows as $index => $row){
            $rowNumber = $index + 2;

            if((int)$row['current_revision'] > 0 && $row['status'] !== 'Deleted'){
                try{
                    $this->importRow($row, $rowNumber);
                    $this->imported++;
                }catch (\Exception $e){
                    $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
        }

        // Process the deletions for last
        foreach($rows as $index => $row){
            $rowNumber = $index + 2;
            if($row['status'] === 'Deleted'){
                try {
                    $this->importDeletion($row, $rowNumber);
                    $this->imported++;
                } catch (\Exception $e){
                    $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
        }

        if(!empty($this->errors)) {
            throw new \Exception(json_encode($this->errors));
        }
    }

    protected function importRow($row, $rowNumber){
        // \Log::info("Row {$rowNumber} data: ", $row->toArray());
        $isOriginal = (int)$row['current_revision'] === 0;
        $originalDocId = null;

        // Map Originating section and normalize source type
        $originatingSection = OfficeMapper::map($row['originating_section']);
        $sourceType = OfficeMapper::normalizeSourceType($row['source_type']);

        $registeredAt = !empty($row['registered_at'])
                        ? Carbon::parse($row['registered_at'])
                        : now();
        $supersededAt = !empty($row['superseded_at'])
                        ? Carbon::parse($row['superseded_at'])
                        : null;
        $deletedAt = !empty($row['deleted_at'])
                    ?Carbon::parse($row['deleted_at'])
                    : null;
        // Revision Logic
        if(!$isOriginal){
            $original = IsoMasterDocument::where('document_code', $row['document_code'])
                                        ->where('current_revision', 0)
                                        ->first();
            if (!$original){
                throw new \Exception("Revision requires original document with code {$row['document_code']} to exist first.");
            }
            $originalDocId = $original->id;
            
            $original->update([
                'status' => 'Superseded',
                'superseded_at' => $registeredAt // use the date of the registered document to mark
                // The date where the original document is superseded at
            ]);
        }
        
        IsoMasterDocument::create([
            'document_code' => $row['document_code'],
            'document_title' => $row['document_title'],
            'source_type' => $sourceType,
            'specific_type' => $row['specific_type'] ?? null,
            'originating_section' => $originatingSection,
            'current_revision' => (int)$row['current_revision'],
            'is_original' => $isOriginal,
            'original_document_id' => $originalDocId,
            'status' => $row['status'] ?? 'Active',
            'registered_at' => $registeredAt,
            'superseded_at' => $supersededAt,
            'deleted_at' => $deletedAt,
            'source' => 'excel',
            'ticket_id' => null,
            'ticket_document_id' => null,
        ]);
    }

    public function importDeletion($row, $rowNumber){
        // Find the document to delete
        $docToDelete = IsoMasterDocument::where('document_code', $row['document_code'])
                                        ->where('current_revision', (int)$row['current_revision'])
                                        ->where('status', '!=', 'Deleted')
                                        ->first();
        if(!$docToDelete){
            throw new \Exception("Cannot delete document {$row['document_code']} rev: {$row['current_revision']} - document doesn't exit or is already deleted.");
        }

        //Get the value of the deleted date
        $deletedDate = $row['deleted_at'];
        // Mark original as superseded
        $docToDelete->update([
            'status' => 'Superseded',
            'superseded_at' => $deletedDate
        ]);
        
        // Map originating section and normalize source type
        $originatingSection = OfficeMapper::map($row['originating_section']);
        $sourceType = OfficeMapper::normalizeSourceType($row['source_type']);

        // Create deletion record audit
        $deletedAt = !empty($row['deleted_at'])
                ? Carbon:: parse($row['deleted_at'])
                : now();
        // \Log::info("Creating Deletion record with registered_at: " . $docToDelete->registered_at);
        IsoMasterDocument::create([
            'document_code' => $docToDelete->document_code,
            'document_title' => $docToDelete->document_title,
            'source_type' => $sourceType,
            'specific_type' => $docToDelete->specific_type,
            'originating_section' => $originatingSection,
            'current_revision' => $docToDelete->current_revision,
            'is_original' => false,
            'original_document_id' => $docToDelete->original_document_id ?? $docToDelete->id,
            'status' => 'Deleted',
            'registered_at' => $row['registered_at'],
            'deleted_at' => $deletedAt,
            'source' => 'excel',
            'ticket_id' => null,
            'ticket_document_id' => null,
        ]);
    }

    public function getImportedCount(){
        return $this->imported;
    }

}
