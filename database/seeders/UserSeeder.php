<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin (Full Access)
        User::updateOrCreate(
            ['email' => 'superadmin@dasen.id'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('dasendco'),
                'role'     => 'superadmin',
            ]
        );

        // Staff Gudang (Read-Only)
        User::updateOrCreate(
            ['email' => 'staff@dasen.id'],
            [
                'name'     => 'Staff Gudang',
                'password' => Hash::make('dasendco'),
                'role'     => 'user',
            ]
        );

        // Remove old admin@dasen.id if exists
        User::where('email', 'admin@dasen.id')->orWhere('role', 'admin')->delete();
    }
}
