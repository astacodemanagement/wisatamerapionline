<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';
    protected $guarded = [];

    protected $casts = [
        'publish_date' => 'datetime',
    ];
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
}
