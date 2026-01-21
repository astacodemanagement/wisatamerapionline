<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    protected $guarded = [];
    
    public function sub_services()
    {
        return $this->hasMany(SubService::class);
    }
}
