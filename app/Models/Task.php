<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'project_user_id',
        'task_state_id',
        'start_date',
        'end_date',
        'user_id',
    ];

    public function userProject()
    {
        return $this->belongsTo(UserProject::class, 'project_user_id');
    }

    public function state()
    {
        return $this->belongsTo(TaskState::class, 'task_state_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
