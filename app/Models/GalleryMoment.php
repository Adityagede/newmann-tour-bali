<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryMoment extends Model
{
    protected $fillable = [
        'title',
        'caption',
        'category',
        'location',
        'alt_text',
        'image_path',
        'display_size',
        'sort_order',
        'is_featured',
        'status',
        
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];
}