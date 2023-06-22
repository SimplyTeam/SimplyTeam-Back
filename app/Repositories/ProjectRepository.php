<?php

namespace App\Repositories;

use App\Models\Project;

class ProjectRepository {
    public function save(Project $project) {
        $project->save();
    }

    public function getProjectsByWorkspaceId($workspaceId) {
        return Project::where('workspace_id', $workspaceId);
    }
}
