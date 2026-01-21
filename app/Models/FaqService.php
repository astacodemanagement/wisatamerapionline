<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqService extends Model
{
    protected $table = 'faq_services';
    protected $guarded = [];

      public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }
   
}
