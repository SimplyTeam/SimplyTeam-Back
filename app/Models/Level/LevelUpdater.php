<?php

namespace App\Models\Level;

use App\Models\Level;
use App\Models\User;

class LevelUpdater implements LevelUpdaterInterface
{
    private User $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function updateLevel() : void {
        $user = $this->user;

        $newLevel = Level::query()
            ->where('min_point', '>=', $user->earned_points)
            ->where('max_point', '<=', $user->earned_points)
            ->first();

        if($newLevel && $newLevel != $user->level) {
            $user->level_id = $newLevel->id;
        }
    }
}
