<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class configurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('configurations')->insert([
            "user_id" => 1,
            "periode_proche_perimee" => 60,
            "periode_proche_terminee" => 30,
            "created_at" => "2023-05-22"
        ]);
    }
}
