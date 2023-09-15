<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StatusSeeder::class,
            PrioritySeeder::class,
            LevelSeeder::class
        ]);
        $this->call(LevelSeeder::class);
        $this->call(QuestTypeSeeder::class);
        $this->call(MaitreDuTempsQuestSeeder::class);
        $this->call(TravailDurQuestSeeder::class);
        $this->call(MarathonDesSprintsQuestSeeder::class);
    }
}
