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
        return $this->hasMany(Sprint::class)->with('tasks');
    }

    public function backlog()
    {
        return $this->hasMany(Task::class)->whereNull('sprint_id');
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

    public function hasSprintWithId(string $sprintId)
    {
        return $this->sprints()->findOrFail($sprintId)->exists();
    }
}
