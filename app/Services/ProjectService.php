<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;

class ProjectService {

    public  function __construct(ProjectRepository $projectRepository) {
        $this->projectRepository = $projectRepository;
    }

    public function getProjectsByWorkspaceId($workspaceId) {
        return $this->projectRepository->getProjectsByWorkspaceId($workspaceId);
    }
}
