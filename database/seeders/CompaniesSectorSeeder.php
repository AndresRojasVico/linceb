<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CompaniesSectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('company_sector')->insert([
            [
                'company_id' => 1,
                'sector_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => 1,
                'sector_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
