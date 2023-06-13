<?php

namespace Database\Seeders;

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
        DB::table('priorities')->insert([
            ['id' => 1, 'label' => 'Low'],
            ['id' => 2, 'label' => 'Medium'],
            ['id' => 3, 'label' => 'High'],
        ]);
    }
}
