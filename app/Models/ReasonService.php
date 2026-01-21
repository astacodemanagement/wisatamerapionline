<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReasonService extends Model
{
    protected $table = 'reason_services';
    protected $guarded = [];

       public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }
   
}
