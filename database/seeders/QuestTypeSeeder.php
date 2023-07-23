<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Quest;
use App\Models\QuestType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuestType::firstOrCreate([
            'id' => 1,
            'label' => "Global"
        ]);

        QuestType::firstOrCreate([
            'id' => 2,
            'label' => "Tasks"
        ]);

        QuestType::firstOrCreate([
            'id' => 3,
            'label' => "Sprints"
        ]);
    }
}
