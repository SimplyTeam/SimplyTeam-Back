<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maxPoint = 100;

        Level::firstOrCreate([
            'id' => 1,
            'max_point' => $maxPoint,
            'min_point' => 0,
        ]);

        $numberToIncrement = 200;
        $multiplicator = 1;
        $maxLevel = 50;

        for($levelNumber = 2; $levelNumber<=$maxLevel; $levelNumber++) {
            $minPoint = $maxPoint + 1;
            $maxPoint += $numberToIncrement * $multiplicator;
            if ($multiplicator < 20) {
                $multiplicator += 1;
            }
            $level = Level::find($levelNumber);
            if($level) {
                $levelData = [
                    'max_point' => $maxPoint,
                    'min_point' => $minPoint
                ];
                $level->update($levelData);
            }
            else {
                $level = [
                    'id' => $levelNumber,
                    'max_point' => $maxPoint,
                    'min_point' => $minPoint
                ];
                Level::firstOrCreate($level);
            }
        }
    }
}
