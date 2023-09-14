<?php

namespace App\Models;

use App\Http\Resources\WorkspaceResource;
use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        return $this
            ->belongsToMany(
                User::class,
                'link_between_users_and_workspaces',
                'workspace_id',
                'user_id')
            ->withPivot('is_PO');
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

    public function linksBetweenUsersAndWorkspaces()
    {
        return $this->hasMany(LinkBetweenUsersAndWorkspaces::class, 'workspace_id');
    }

    public function hasProject(Project $project) {
        return $this->projects()->where('id', $project->id)->exists();
    }
}
