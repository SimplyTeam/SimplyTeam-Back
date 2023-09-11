<?php

namespace App\Services;

use App\Models\User;
use App\Models\Workspace;

class WorkspaceService
{
    public function checkIfUserIsPOOfWorkspace($user, $workspace) {
        return $workspace->users()->where('id', $user->id)->where('is_PO', true)->exists();
    }

    public function getNumberOfWorkspaceOfUser(User $user) : int {
        return $user->workspaces()->count();
    }

    public function userIsAllowToCreateWorkspace(User $user) : bool {
        return $user->isPremiumValid(true) || (
            $this->getNumberOfWorkspaceOfUser($user) == 0
        );
    }

    public function userCanInviteNUsersInWorkspaceIsAllow (
        User $user,
        int $numberOfUserToInvite,
        Workspace $workspace
    ) : bool  {
        $numberOfUserInWorkspace = 0;

        if($workspace) {
            $numberOfUserInWorkspace = $workspace->users()->count();
        }

        $numberOfInvitationInWorkspace = $workspace->invitations()->count();

        $totalInvitedUserCount = $numberOfUserInWorkspace + $numberOfInvitationInWorkspace + $numberOfUserToInvite;

        return $user->isPremiumValid(true) || $totalInvitedUserCount <= 8;
    }
}
