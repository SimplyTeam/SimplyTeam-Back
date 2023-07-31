<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Priority::firstOrCreate(
            ['id' => 1, 'label' => 'Low']
        );

        Priority::firstOrCreate(
            ['id' => 2, 'label' => 'Medium']
        );

        Priority::firstOrCreate(
            ['id' => 3, 'label' => 'High']
        );
    }
}
