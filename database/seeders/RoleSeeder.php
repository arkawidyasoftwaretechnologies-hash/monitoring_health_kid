<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'description' => 'Admin Dinkes'],
            ['name' => 'admin_puskesmas', 'description' => 'Admin Puskesmas'],
            ['name' => 'petugas', 'description' => 'Petugas Posyandu/Kader'],
            ['name' => 'kader', 'description' => 'Kader Lapangan'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
