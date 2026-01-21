<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $table = 'designs';
    protected $guarded = [];

       public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }
   
}
