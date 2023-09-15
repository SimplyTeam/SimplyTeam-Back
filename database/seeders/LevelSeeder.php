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

        $numberToIncrement = 100;
        $maxLevel = 50;

        for($levelNumber = 2; $levelNumber<=$maxLevel; $levelNumber++) {
            $minPoint = $maxPoint + 1;
            $maxPoint += $numberToIncrement;
            $level = [
                'id' => $levelNumber,
                'max_point' => $maxPoint,
                'min_point' => $minPoint
            ];
            Level::firstOrCreate($level);
        }
    }
}
