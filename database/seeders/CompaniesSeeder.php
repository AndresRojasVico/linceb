<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => 'Empresa 1',
                'created_at' => now(),
                'updated_at' => now(),
                'nif' => '123456789',
            ],
            [
                'name' => 'Empresa 2',
                'created_at' => now(),
                'updated_at' => now(),
                'nif' => '987654321',
            ],

        ]);
    }
}
