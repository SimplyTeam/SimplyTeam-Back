<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([
            ['id' => 1, 'label' => 'New'],
            ['id' => 2, 'label' => 'In Progress'],
            ['id' => 3, 'label' => 'Done'],
        ]);
    }
}
