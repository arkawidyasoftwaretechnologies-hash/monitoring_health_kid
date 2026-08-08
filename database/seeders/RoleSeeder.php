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
            ['name' => 'admin', 'description' => 'Administrator Utama'],
            ['name' => 'operator', 'description' => 'Bidan / Asisten Medis / Kader'],
            ['name' => 'dokter', 'description' => 'Dokter Anak'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
