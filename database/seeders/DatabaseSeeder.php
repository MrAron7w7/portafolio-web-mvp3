<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario admin de prueba
        $admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
        ]);

        // Crear usuario normal de prueba
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        // Ejecutar seeder de roles y permisos
        $this->call(RolesAndPermissionsSeeder::class);

        // Asignar roles
        $admin->assignRole('admin');
        $user->assignRole('user');
    }
}
