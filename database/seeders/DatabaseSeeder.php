<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Fix duplicate user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // penting kalau tak ada
            ]
        );

        // Ini dah betul (tak duplicate)
        SystemSetting::updateOrCreate(
            ['key' => 'app_name'],
            ['value' => 'WakMusic']
        );

        SystemSetting::updateOrCreate(
            ['key' => 'hero_background'],
            ['value' => 'brand/hero_bg.jpg']
        );
    }
}
