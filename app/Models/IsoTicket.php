<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class IsoTicket extends Model
{
    // Assign Laravel Which table this model uses
    protected $table = 'iso_tickets';


    // Which fields can be mass-assigned
    protected $fillable = [
        'originating_section',
        'sharepoint_link',
        'message_to_idc',
        'status',
        'created_by'
    ];

    // Define relationship: One-to-many -> One Ticket Many Documents
    public function documents(){
        return $this->hasMany(IsoTicketDocument::class, 'ticket_id');
    }

    // Define relationship: Ticket belongs to a user
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }

    // Add a new relationship to get the employee info directly
    public function creatorProfile(){
        return $this->hasOneThrough(
            Employee::class,
            User::class,
            'id',
            'emp_id',
            'created_by',
            'id'
        );
    }
}
