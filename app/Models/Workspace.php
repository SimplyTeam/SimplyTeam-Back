<?php

namespace App\Models;

use App\Http\Resources\WorkspaceResource;
use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema(
 *     schema="Workspace",
 *     description="Workspace model",
 *     title="Workspace",
 *     required={"name", "description"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the Workspace",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the Workspace",
 *         example="My Workspace"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the Workspace",
 *         example="A brief description about the workspace"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Time when the Workspace was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last time the Workspace was updated"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         format="date-time",
 *         description="Time when the Workspace was deleted (if soft deleted)"
 *     ),
 *     @OA\Property(
 *         property="created_by_id",
 *         type="integer",
 *         description="ID of the user who created the Workspace",
 *         example=2
 *     )
 * )
 */
class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        "id"
    ];

    public function show(Workspace $workspace)
    {
        $workspace->load('createdBy');

        return new WorkspaceResource($workspace);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'link_between_users_and_workspaces', 'workspace_id', 'user_id');
    }

    public function invitations()
    {
        return $this->hasMany(WorkspaceInvitation::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::factory(new WorkspaceFactory());
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function hasProject(Project $project) {
        return $this->projects()->where('id', $project->id)->exists();
    }
}
