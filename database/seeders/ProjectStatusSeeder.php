<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //  
        DB::table('project_status')->insert([
            [
                'name' => 'Pendiente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'En Proceso',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Completada',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


    }
}
