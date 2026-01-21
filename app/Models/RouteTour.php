<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteTour extends Model
{
    
    protected $table = 'route_tours';
    protected $guarded = [];

    public function tour()
{
    return $this->belongsTo(Tour::class);
}


    
}