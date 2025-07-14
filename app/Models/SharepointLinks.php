<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharepointLinks extends Model
{
    protected $table = 'sharepoint_links';

    protected $fillable = [
        'category',
        'department',
        'office',
        'label',
        'url',
        'description',       
    ];
}
