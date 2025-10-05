<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le rôle Super Admin s'il n'existe pas
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        
        // Créer toutes les permissions nécessaires
        $permissions = [
            'manage_schools',
            'manage_users',
            'manage_parameters',
            'view_analytics',
            'manage_roles',
            'manage_permissions',
            'access_admin_panel'
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Assigner toutes les permissions au rôle Super Admin
        $superAdminRole->syncPermissions($permissions);
        
        // Créer l'utilisateur Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'marcosseko00@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'marcosseko00@gmail.com',
                'password' => Hash::make('Yoto1975@'),
                'email_verified_at' => now(),
                'global_role' => 'super_admin',
                'is_active' => true,
                'profile_data' => json_encode([
                    'phone' => null,
                    'avatar_url' => null,
                    'bio' => 'Super Administrateur de NetNote',
                    'preferences' => [
                        'language' => 'fr',
                        'timezone' => 'Africa/Porto-Novo',
                        'notifications' => true
                    ]
                ])
            ]
        );
        
        // Assigner le rôle Super Admin à l'utilisateur
        $superAdmin->assignRole('super_admin');
        
        $this->command->info('Super Admin créé avec succès !');
        $this->command->info('Email: marcosseko00@gmail.com');
        $this->command->info('Mot de passe: Yoto1975@');
    }
}
