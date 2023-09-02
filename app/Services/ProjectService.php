<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;

class ProjectService
{
    public function getNumberOfProjectOfWorkspaceOfUser(User $user, Workspace $workspace){
        return $workspace->projects()->count();
    }

    public function isUserAllowedToCreateProjectInWorkspaceWithSubscription(User $user, Workspace $workspace) {
        return $user->isPremiumValid() || (
            $this->getNumberOfProjectOfWorkspaceOfUser($user, $workspace) < 2
        );
    }
}
