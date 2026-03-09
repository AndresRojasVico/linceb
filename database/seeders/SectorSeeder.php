<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sectors')->insert([
            [
                'name' => 'IT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'limpieza',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Servicios',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
