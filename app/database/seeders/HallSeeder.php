<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    DB::table('halls')->insert([
        ['name' => 'Salle 1', 'capacity' => 150, 'type' => 'normal'],
        ['name' => 'Salle VIP', 'capacity' => 50, 'type' => 'vip'],
        ['name' => 'Salle 3D', 'capacity' => 120, 'type' => 'normal']
    ]);
}
}
