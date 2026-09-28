<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\IsoTicket;
use App\Models\IsoTicketDocument;

class DocumentStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public IsoTicketDocument $document;
    public IsoTicket $ticket;
    public string $oldDocStatus;
    public string $changedBy;
    public int $approvedCount;
    public int $totalCount;
    public int $onHoldCount;
    public int $underReviewCount;
    public float $progressPercentage;

    /**
     * Create a new message instance.
     */
    public function __construct(IsoTicketDocument $document, IsoTicket $ticket, string $oldDocStatus, string $changedBy)
    {
        $this->document = $document;
        $this->ticket = $ticket;
        $this->oldDocStatus = $oldDocStatus;
        $this->changedBy = $changedBy;

        // Eager load ticket documents if not already loaded
        $allDocs = $ticket->relationLoaded('documents') ? $ticket->documents : $ticket->documents()->get();
        $this->totalCount = max(1, $allDocs->count());
        $this->approvedCount = $allDocs->where('status', 'approved')->count();
        $this->onHoldCount = $allDocs->where('status', 'on_hold')->count();
        $this->underReviewCount = max(0, $this->totalCount - $this->approvedCount - $this->onHoldCount);
        $this->progressPercentage = round(($this->approvedCount / $this->totalCount) * 100);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $docCode = $this->document->document_code;
        $tktNum = $this->ticket->ticket_number;

        if ($this->document->status === 'approved') {
            $subject = "[Partial Approval] Document {$docCode} Approved — Ticket #{$tktNum} ({$this->approvedCount} of {$this->totalCount} Approved)";
        } else {
            $subject = "[Action Required] Document {$docCode} Placed On-Hold — Ticket #{$tktNum}";
        }

        return $this->subject($subject)
            ->view('emails.document-status-updated')
            ->with([
                'recipientName' => $this->ticket->creator->name ?? 'Document Handler'
            ]);
    }
}
