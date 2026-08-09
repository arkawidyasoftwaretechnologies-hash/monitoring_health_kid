<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            FullWhoReferenceSeeder::class,
            DemoSeeder::class,
        ]);

        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        $operatorRole = \App\Models\Role::where('name', 'operator')->first();
        $dokterRole = \App\Models\Role::where('name', 'dokter')->first();

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@klinik.com',
            'password' => bcrypt('rahasia123'),
            'role_id' => $adminRole->id,
        ]);

        User::factory()->create([
            'name' => 'Bidan Siti',
            'email' => 'bidan@klinik.com',
            'password' => bcrypt('rahasia123'),
            'role_id' => $operatorRole->id,
        ]);

        User::factory()->create([
            'name' => 'Dr. Andi Sp.A',
            'email' => 'dokter@klinik.com',
            'password' => bcrypt('rahasia123'),
            'role_id' => $dokterRole->id,
        ]);
    }
}
