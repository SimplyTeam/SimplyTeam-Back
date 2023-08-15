<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Reward;
use App\Models\Workspace;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    /**
     * Display a listing of the projects for a user in the given workspace.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $user = $request->user();

        # Get levels
        $numberOfNextLevel = Level::where('id', '>', $user->level->id)
            ->count();
        $numberOfPreviousLevel = Level::where('id', '<', $user->level->id)
            ->count();
        $numberOfLevelsToReturn = 8;
        $numberOfNextLevelToReturn = 4;
        $numberOfPreviousLevelToReturn = 3;

        if (
            $numberOfNextLevel > $numberOfNextLevelToReturn &&
            $numberOfPreviousLevel < $numberOfPreviousLevelToReturn
        ) {
            $numberOfPreviousLevelToReturn += $numberOfPreviousLevel - $numberOfPreviousLevelToReturn;
            $numberOfNextLevelToReturn = $numberOfLevelsToReturn - 1 - $numberOfPreviousLevelToReturn;
        } elseif (
            $numberOfNextLevel < $numberOfNextLevelToReturn &&
            $numberOfPreviousLevel > $numberOfPreviousLevelToReturn
        ) {
            $numberOfPreviousLevelToReturn = $numberOfLevelsToReturn - 1;
            $numberOfNextLevelToReturn = 0;
        }

        $levels = Level::where('id', '>=', $user->level->id - $numberOfPreviousLevelToReturn)
            ->where('id', '<=', $user->level->id + $numberOfNextLevelToReturn)
            ->orderBy('id')
            ->get();

        foreach ($levels as $level) {
            $level->status = $level->getStatusLevelOfAuthenticatedUserAttribute();
        }

        $rewards = $user->rewards;

        return [
            "levels" => $levels,
            "rewards" => $rewards
        ];
    }
}
