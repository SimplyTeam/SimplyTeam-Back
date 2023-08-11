<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Quest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TravailDurQuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maxLevel = 20;
        $rewardsPoint = 50;
        $questName = "Travail Dur";
        $image = "travail-dur.svg";

        $NOfQuest = ["1", "2", "4", "8", "16"];
        $id = Quest::query()->max('id') + 1;
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

            // Determine grade based on the level.
            $grade = 'bronze'; // default value
            if ($levelNumber > ($maxLevel * 2 / 3)) {
                $grade = 'gold';
            } elseif ($levelNumber > ($maxLevel / 3)) {
                $grade = 'silver';
            }

            $newData = [
                'name' => $questName,
                'description' =>
                    "Finir $numberOfElementToComplete "
                    . ($numberOfElementToComplete > 1 ? "tâches" : "tâche")
                    . ".",
                'reward_points' => $rewardsPoint,
                'count' => $numberOfElementToComplete,
                'level' => $levelNumber,
                'previous_quest_id' => $previousId,
                'quest_types_id' => 2,
                'grade' => $grade,
                'image' => $image
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
