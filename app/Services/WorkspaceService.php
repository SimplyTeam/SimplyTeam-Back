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

    /**
     * Allow to set or unset user po on workspace
     * @param User $user
     * @param Workspace $workspace
     * @return void
     * @throws \Exception
     */
    public function setOrUnsetUserPOOnWorkspace(User $user, Workspace $workspace) {
        // Check if the user is already associated with the workspace
        $pivot = $workspace->users()->where('user_id', $user->id)->first();

        if(is_null($pivot)) {
            throw new \Exception("L'utilisateur n'est pas rattaché au workspace");
        }

        // Toggle the is_po attribute
        $isPOValue = 1;

        // Update the pivot attribute
        $workspace->users()->updateExistingPivot($user->id, ['is_po' => $isPOValue]);
    }

    /**
     * Allow to set user po on workspace
     * @param User $user
     * @param Workspace $workspace
     * @return void
     * @throws \Exception
     */
    public function setUserPOOnWorkspace(User $user, Workspace $workspace): void
    {
        // Check if the user is already associated with the workspace
        $pivot = $workspace->users()->where('user_id', $user->id)->first();

        if(is_null($pivot)) {
            throw new \Exception("L'utilisateur n'est pas rattaché au workspace");
        }

        // Toggle the is_po attribute
        $isPOValue = 1;

        // Update the pivot attribute
        $workspace->users()->updateExistingPivot($user->id, ['is_po' => $isPOValue]);
    }

    /**
     * Allow to set user po on workspace
     * @param User $user
     * @param Workspace $workspace
     * @return void
     * @throws \Exception
     */
    public function unsetUserPOOnWorkspace(User $user, Workspace $workspace): void
    {
        // Check if the user is already associated with the workspace
        $pivot = $workspace->users()->where('user_id', $user->id)->first();

        if(is_null($pivot)) {
            throw new \Exception("L'utilisateur n'est pas rattaché au workspace");
        }

        // Toggle the is_po attribute
        $isPOValue = 0;

        // Update the pivot attribute
        $workspace->users()->updateExistingPivot($user->id, ['is_po' => $isPOValue]);
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
