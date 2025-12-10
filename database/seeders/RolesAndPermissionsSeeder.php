<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Permisos de portafolios
            'portfolios.view',
            'portfolios.create',
            'portfolios.edit',
            'portfolios.delete',
            
            // Permisos de usuarios (admin)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Permisos de configuración (admin)
            'settings.view',
            'settings.edit',
            
            // Permisos de reportes (admin)
            'reports.view',
            'reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        // Rol: Usuario normal
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'portfolios.view',
            'portfolios.create',
            'portfolios.edit',
            'portfolios.delete',
        ]);

        // Rol: Administrador
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Asignar rol admin al primer usuario (si existe)
        $firstUser = User::first();
        if ($firstUser && !$firstUser->hasRole('admin')) {
            $firstUser->assignRole('admin');
        }

        $this->command->info('Roles y permisos creados exitosamente.');
    }
}
