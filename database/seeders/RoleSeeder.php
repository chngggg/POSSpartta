<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Akses penuh ke seluruh sistem'
        ]);

        Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Mengelola sparepart dan kategori'
        ]);

        Role::create([
            'name' => 'Pegawai',
            'slug' => 'employee',
            'description' => 'Hanya transaksi penjualan'
        ]);
    }
}
