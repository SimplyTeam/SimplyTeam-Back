<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'description',
        'estimated_timestamp',
        'realized_timestamp',
        'deadline',
        'is_finish',
        'sprint_id',
        'priority_id',
        'status_id',
        'project_id',
        'created_by',
        'parent_id',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sprint()
    {
        return $this->belongsTo(Sprint::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_tasks');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id')->with(['users', 'createdBy', 'parent', 'sprint']);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function getDeadline($task)
    {
        return $task->deadline ? $task->deadline : null;
    }
}
