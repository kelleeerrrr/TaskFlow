<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@taskbuilder.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@taskbuilder.com'],
            [
                'name' => 'Test Manager',
                'password' => Hash::make('manager123'),
                'role' => 'manager',
                'status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'status' => 'active',
            ]
        );
    }
}
