<?php

namespace App\Services;

use App\Models\User;

class WorkspaceService
{
    public function getNumberOfWorkspaceOfUser(User $user){
        return $user->workspaces()->count();
    }

    public function userIsAllowToCreateWorkspace(User $user) {
        return $user->isPremiumValid() || (
            $this->getNumberOfWorkspaceOfUser($user) == 0
        );
    }
}
