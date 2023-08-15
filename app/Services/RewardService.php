<?php

namespace App\Services;

use App\Models\Level;
use App\Models\Reward;
use App\Models\User;

class RewardService
{
    public function getFirstUnassignedRewardWhenLevelUp(Level $level){
        return Reward::query()
            ->where('level_id', '=', $level->id)
            ->whereNull('user_id')
            ->first();
    }

    public function getFirstAssignedRewardWithLevelAndUser(Level $level, User $user)
    {
        return Reward::query()
            ->where('level_id', '=', $level->id)
            ->where('user_id', '=', $user->id)
            ->first();
    }
}
