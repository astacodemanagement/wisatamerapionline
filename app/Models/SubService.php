<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubService extends Model
{
    protected $table = 'sub_services';
    protected $guarded = [];

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    public function faqServices()
    {
        return $this->hasMany(FaqService::class);
    }
    public function reasonServices()
    {
        return $this->hasMany(ReasonService::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
