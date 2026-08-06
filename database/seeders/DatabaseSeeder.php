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

        $superAdminRole = \App\Models\Role::where('name', 'super_admin')->first();

        User::factory()->create([
            'name' => 'Super Admin Dinkes',
            'email' => 'admin@dinkes.go.id',
            'password' => bcrypt('password'),
            'role_id' => $superAdminRole->id,
        ]);
    }
}
