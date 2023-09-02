<?php

namespace App\Models\Level;

use App\Models\Reward;
use App\Models\User;
use App\Services\RewardService;

class UserRewardAssignment
{
    private User $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function assignAllowedRewardOnUser() {
        if (!$this->user->isPremiumValid(true)) {
           return null;
        }

        $rewardService = new RewardService();

        $assignedRewardOfCurrentLevel = $rewardService->getFirstAssignedRewardWithLevelAndUser(
            $this->user->level,
            $this->user
        );

        if($assignedRewardOfCurrentLevel) {
            return;
        }

        $rewardToAssign = $rewardService->getFirstUnassignedRewardWhenLevelUp($this->user->level);

        if($rewardToAssign) {
            $rewardToAssign->user_id = $this->user->id;
            $rewardToAssign->save();
        }

        return $rewardToAssign;
    }
}
