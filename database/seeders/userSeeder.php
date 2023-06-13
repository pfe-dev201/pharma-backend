<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'nom' => 'admin',
            'prenom' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'status' => 'SUPER',
            'role_id' => 1,
            "created_at" => "2023-05-22"
        ]);
    }
}
