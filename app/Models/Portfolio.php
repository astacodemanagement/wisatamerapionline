<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $table = 'portfolios';
    protected $guarded = [];

    public function sub_service()
    {
        return $this->belongsTo(SubService::class);
    }
}
