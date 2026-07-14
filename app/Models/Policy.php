<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'title',
        'category',
        'url',
        'description',
        'status',
        'document_code',
        'revision_count',
        'effectivity_date',
        'policy_date',
    ];

    protected $casts = [
        'revision_count' => 'integer',
    ];
}
