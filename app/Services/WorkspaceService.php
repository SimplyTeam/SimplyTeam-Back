<?php

namespace App\Services;

use App\Exceptions\WorkspaceNotOwnedException;
use App\Models\Project;
use App\Repositories\ProjectRepository;
use App\Repositories\WorkspaceRepository;

class WorkspaceService {

    public  function __construct(WorkspaceRepository $workspaceRepository) {
        $this->workspaceRepository = $workspaceRepository;
    }

    private function isWorkspaceOwner($userId, $workspaceId) {
        $workspace = $this->workspaceRepository->getById($workspaceId);

        if($workspace->user_id != $userId) {
            throw new WorkspaceNotOwnedException();
        }
    }

    public function getByUserId($userId, $workspaceId) {
        $userWorkspace = $this->workspaceRepository->getById($workspaceId);

        if($userWorkspace->user_id != $userId) {
            throw new WorkspaceNotOwnedException();
        }

        if (!$workspace) {
            throw new WorkspaceNotOwnedException();
        }
        return $this->workspaceRepository->getByUserId($userId);
    }
}
