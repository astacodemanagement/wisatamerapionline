<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
    protected $guarded = [];

    public function category()
{
    return $this->belongsTo(ProjectCategory::class, 'project_category_id');
}
}
