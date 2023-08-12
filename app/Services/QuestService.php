<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserQuest;

class QuestService
{
    public function updateQuest($user, $questName, $finishedAt, $deadline = null)
    {
        $currentQuest = $user->quests()
            ->where('name', $questName)
            ->where('users_quests.in_progress', true)
            ->first();

        if ($currentQuest && ($finishedAt < $deadline || $deadline == null)) {
            $currentQuest->pivot->completed_count++;
            $currentQuest->pivot->is_completed = $currentQuest->pivot->completed_count == $currentQuest->count;
            $currentQuest->pivot->date_completed = $currentQuest->pivot->is_completed ? Carbon::now() : null;
            $currentQuest->pivot->in_progress = !$currentQuest->pivot->is_completed;
            $currentQuest->pivot->save();

            if ($currentQuest->level < 20 && $currentQuest->pivot->is_completed) {
                UserQuest::query()
                    ->join('quests', 'users_quests.quest_id', '=', 'quests.id')
                    ->where('user_id', $user->id)
                    ->where('name', $questName)
                    ->where('users_quests.in_progress', false)
                    ->where('level', $currentQuest->level + 1)
                    ->update(['in_progress' => true, 'completed_count' => 0]);
            }

            if ($currentQuest->pivot->is_completed) {
                $user->earned_points += $currentQuest->reward_points;
            }
        }
    }
}
