<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    protected $table = 'project_status';

    protected $fillable = ['name'];

    public function userProjects()
    {
        return $this->hasMany(UserProject::class);
    }
}
