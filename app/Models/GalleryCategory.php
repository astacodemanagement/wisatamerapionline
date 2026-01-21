<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // INI YANG HARUS DITAMBAHKAN

class GalleryCategory extends Model
{
    use HasFactory;
    
    protected $table = 'gallery_categories';
    protected $guarded = [];

    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'gallery_category_id');
    }
}