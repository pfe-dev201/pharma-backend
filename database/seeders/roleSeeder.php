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
                'type' => 'ECRIRE-LIRE',
                'description' => "insértion des médicaments - consultation de l'état et des rapports - configuration de l'application",
                "created_at" => "2023-05-22"
            ],
            [
                'type' => 'ECRIRE',
                'description' => "insértion des médicaments - consultation de l'état et des rapports",
                "created_at" => "2023-05-22"
            ],
            [
                'type' => 'LIRE',
                'description' => "consultation de l'état et des rapports",
                "created_at" => "2023-05-22"
            ],
        ]);
    }
}
