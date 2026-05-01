<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID roles
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $adminRole = Role::where('slug', 'admin')->first();
        $employeeRole = Role::where('slug', 'employee')->first();

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@sparttapos.com',
            'password' => Hash::make('password123'),
            'role_id' => $superAdminRole->id,
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@sparttapos.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Pegawai User',
            'email' => 'pegawai@sparttapos.com',
            'password' => Hash::make('password123'),
            'role_id' => $employeeRole->id,
        ]);
    }
}
