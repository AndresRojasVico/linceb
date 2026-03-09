<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class TaskStatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('task_states')->insert([
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
