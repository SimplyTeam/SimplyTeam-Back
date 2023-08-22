<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Sprint",
 *     type="object",
 *     title="Sprint",
 *     description="Sprint model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier for the sprint",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the sprint"
 *     ),
 *     @OA\Property(
 *         property="begin_date",
 *         type="string",
 *         format="date",
 *         description="The start date of the sprint"
 *     ),
 *     @OA\Property(
 *         property="end_date",
 *         type="string",
 *         format="date",
 *         description="The end date of the sprint"
 *     ),
 *     @OA\Property(
 *         property="closing_date",
 *         type="string",
 *         format="date",
 *         description="The closing date of the sprint"
 *     ),
 *     @OA\Property(
 *         property="project_id",
 *         type="integer",
 *         description="The associated project's ID for the sprint"
 *     )
 * )
 */
class Sprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'begin_date',
        'end_date',
        'closing_date',
        'project_id'
    ];

    /**
     * Get the project of sprint
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the tasks for the sprint.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class)
            ->with(['users', 'createdBy', 'subtasks', 'sprint'])
            ->whereNull('parent_id');
    }
}
