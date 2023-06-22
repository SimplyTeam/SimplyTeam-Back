<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Workspace;
use Error;
use Exception;

class WorkspaceRepository
{
    public function save(Project $project)
    {
        $project->save();
    }

    public function getById(int $id, ): Workspace
    {
        return Workspace::where('id', $id)->firstOrFail();
    }

    public function getByUserId(int $userId): Workspace
    {
        return Workspace::where('user_id', $userId)->firstOrFail();
    }
}
