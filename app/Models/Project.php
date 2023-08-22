<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Project",
 *     type="object",
 *     title="Project",
 *     description="Project model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the project",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the project",
 *     ),
 *     @OA\Property(
 *         property="workspace_id",
 *         type="integer",
 *         description="The identifier of the associated workspace",
 *     ),
 *     @OA\Property(
 *         property="sprints",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Sprint"),
 *         description="List of sprints associated with the project",
 *     ),
 *     @OA\Property(
 *         property="backlog",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Task"),
 *         description="List of tasks in the backlog for the project",
 *     ),
 *     @OA\Property(
 *         property="tasks",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Task"),
 *         description="List of all tasks associated with the project",
 *     ),
 * )
 */
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
