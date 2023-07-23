<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Quest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaitreDuTempsQuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maxLevel = 20;
        $rewardsPoint = 50;
        $questName = 'Maitre du temps';

        $NOfQuest = ["1", "2", "4", "8", "16"];
        $maxId = Quest::query()->max('id');
        $id = $maxId ? $maxId + 1 : 1;
        $previousId = null;
        $numberOfElementInNOfQuest = count($NOfQuest);

        for ($levelNumber = 1; $levelNumber <= $maxLevel; $levelNumber++) {
            $isLimit = $levelNumber <= $numberOfElementInNOfQuest;

            $rewardsPoint += $isLimit ? 50 : 0;
            $numberOfElementToComplete =
                $isLimit ? $NOfQuest[$levelNumber - 1] : $NOfQuest[$numberOfElementInNOfQuest - 1];

            $quest = Quest::query()
                ->where('name', '=', $questName)
                ->where('level', '=', $levelNumber)
                ->first();

            $newData = [
                'name' => $questName,
                'description' =>
                    "Finir $numberOfElementToComplete "
                    . ($numberOfElementToComplete > 1 ? "tâches" : "tâche")
                    . " sous le temps imparti.",
                'reward_points' => $rewardsPoint,
                'level' => $levelNumber,
                'previous_quest_id' => $previousId,
                'quest_types_id' => 2,
            ];

            if ($quest == null) {
                $newData["id"] = $id;
                $quest = Quest::create($newData);
                $id += 1;
            } else {
                $quest->update($newData);
            }

            $previousId = $quest->id;
        }
    }
}
