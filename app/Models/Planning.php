<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    protected $table = 'plannings';
    protected $guarded = [];

       public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }
   
}
