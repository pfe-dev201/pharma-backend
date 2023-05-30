<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class imageProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = User::all();

        foreach ($records as $record) {
            $record->update([
                'image_profile' => 'default-profile.jpg',
            ]);
        }
    }
}
