<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class roleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'type' => 'read-write',
                'description' => "insértion des médicaments - consultation de l'état et des rapports - configuration de l'application",
                "created_at" => "2023-05-22"
            ],
            [
                'type' => 'write',
                'description' => "insértion des médicaments - consultation de l'état et des rapports",
                "created_at" => "2023-05-22"
            ],
            [
                'type' => 'read',
                'description' => "consultation de l'état et des rapports",
                "created_at" => "2023-05-22"
            ],
        ]);
    }
}
