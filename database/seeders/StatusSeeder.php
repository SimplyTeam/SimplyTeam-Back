<?php

namespace Database\Seeders;

use App\Models\Status;
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
        Status::firstOrCreate(['id' => 1, 'label' => 'New']);
        Status::firstOrCreate(['id' => 2, 'label' => 'In Progress']);
        Status::firstOrCreate(['id' => 3, 'label' => 'Done']);
    }
}
