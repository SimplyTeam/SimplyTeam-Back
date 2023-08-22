<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     title="Task",
 *     description="Task model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the task",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="label",
 *         type="string",
 *         description="The label or title of the task"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the task"
 *     ),
 *     @OA\Property(
 *         property="estimated_timestamp",
 *         type="integer",
 *         description="Estimated time for task completion"
 *     ),
 *     @OA\Property(
 *         property="realized_timestamp",
 *         type="integer",
 *         description="Actual time taken for task completion"
 *     ),
 *     @OA\Property(
 *         property="deadline",
 *         type="string",
 *         format="date",
 *         description="Deadline for the task"
 *     ),
 *     @OA\Property(
 *         property="is_finish",
 *         type="boolean",
 *         description="Indicates if the task is finished or not"
 *     ),
 *     @OA\Property(
 *         property="sprint_id",
 *         type="integer",
 *         description="Associated sprint's ID"
 *     ),
 *     @OA\Property(
 *         property="priority_id",
 *         type="integer",
 *         description="ID of the task's priority"
 *     ),
 *     @OA\Property(
 *         property="status_id",
 *         type="integer",
 *         description="ID of the task's status"
 *     ),
 *     @OA\Property(
 *         property="project_id",
 *         type="integer",
 *         description="Associated project's ID"
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="integer",
 *         description="ID of the user who created the task"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="ID of the parent task if this is a subtask"
 *     )
 * )
 */
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
