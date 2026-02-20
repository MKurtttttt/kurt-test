<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformationHubLinks extends Model
{
    protected $table = 'information_hub_links';

    protected $fillable = [
        'category',
        'sub_category',
        'type',
        'image_path',
        'title',
        'url',       
    ];
}
