<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menu_items';
    protected $guarded = [];
 
    public function group()
    {
        return $this->belongsTo(MenuGroup::class, 'menu_group_id');
    }
 
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->where('status', 'Aktif')->orderBy('position');
    }
 
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }
}
