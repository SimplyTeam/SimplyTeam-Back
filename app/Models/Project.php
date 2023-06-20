<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'workspace_id'
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function sprints()
    {
        return $this->hasMany(Sprint::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function hasTask(Task $task)
    {
        return $this->tasks()->where('id', $task->id)->exists();
    }

    public function hasSprint(Sprint $sprint)
    {
        return $this->sprints()->where('id', $sprint->id)->exists();
    }

    public function hasSprintWithId(string $sprint_id)
    {
        return $this->sprints()->findOrFail($sprint_id)->exists();
    }
}
