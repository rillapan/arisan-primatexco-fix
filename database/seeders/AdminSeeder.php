<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@primatexco.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'is_active' => true,
            ]
        );
    }
}
