<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuGroup extends Model
{
    use HasFactory;

    protected $table = 'menu_groups';
    protected $guarded = [];
   
    public function items()
    {
        return $this->hasMany(MenuItem::class)->where('status', 'Aktif')->whereNull('parent_id')->orderBy('position');
    }
}
