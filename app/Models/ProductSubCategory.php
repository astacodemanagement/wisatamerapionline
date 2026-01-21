<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    protected $table = 'product_sub_categories';
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProductSubCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductSubCategory::class, 'parent_id');
    }
}