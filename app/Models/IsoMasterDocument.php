<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IsoMasterDocument extends Model
{
    protected $fillable = [
        'document_code', 'document_title', 'revised_document_title', 'source_type', 'specific_type',
        'originating_section', 'current_revision', 'is_original', 'original_document_id',
        'status', 'registered_at', 'superseded_at', 'deleted_at', 'source', 'ticket_id',
        'ticket_document_id',
    ];
    protected $casts = [
        'registered_at' => 'datetime',
        'superseded_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_original' => 'boolean',
        'current_revision' => 'integer',
    ];
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(IsoTicket::class);
    }
    public function ticketDocument(): BelongsTo
    {
        return $this->belongsTo(IsoTicketDocument::class);
    }
    public function originalDocument(): BelongsTo
    {
        return $this->belongsTo(IsoMasterDocument::class, 'original_document_id');
    }
    public function revisions(): HasMany
    {
        return $this->hasMany(IsoMasterDocument::class, 'original_document_id');
    }
    public function getRevisionChain()
    {
        if($this->is_original)
        {
            return self::where('id', $this->id)
                ->orWhere('original_document_id', $this->id)
                ->orderBy('current_revision', 'asc')
                ->get();
        } else{
            // if this is a revision, get the original + all siblings
            return self::where('id', $this->original_document_id)
                ->orWhere('original_document_id', $this->original_document_id)
                ->orderBy('current_revision', 'asc')
                ->get();
        }
    }
    public static function getActiveDocuments()
    {
        return self::where('status', 'Active');
    }
}
