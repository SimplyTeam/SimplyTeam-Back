<?php

namespace App\Http\Services;

use App\Models\User;
use Carbon\Carbon;

class QuestService
{
    public function completeQuest(User $user, string $name, Carbon $finishedAt, Carbon $deadline = null)
    {
        $currentQuest = $user->quests()
            ->where('name', $name)
            ->where('users_quests.in_progress', true)
            ->first();

        if ($currentQuest && ($finishedAt->lt($deadline) || $deadline == null)) {
            $this->processQuestCompletion($user, $currentQuest);
        }
    }

    private function processQuestCompletion(User $user, Quest $currentQuest)
    {
        // Logique de traitement de l'achèvement de la quête
    }
}
