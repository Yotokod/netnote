<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Création du Super Admin NetNote ===\n";

try {
    // Vérifier si l'utilisateur existe déjà
    $existingUser = User::where('email', 'marcosseko00@gmail.com')->first();
    
    if ($existingUser) {
        echo "✅ Utilisateur Super Admin existe déjà !\n";
        echo "📧 Email: " . $existingUser->email . "\n";
        echo "👤 Nom: " . $existingUser->name . "\n";
        echo "🔑 Rôle: " . $existingUser->global_role . "\n";
    } else {
        // Créer le rôle Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        
        // Créer les permissions
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
        
        $superAdminRole->syncPermissions($permissions);
        
        // Créer l'utilisateur
        $superAdmin = User::create([
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
        ]);
        
        $superAdmin->assignRole('super_admin');
        
        echo "✅ Super Admin créé avec succès !\n";
        echo "📧 Email: marcosseko00@gmail.com\n";
        echo "🔑 Mot de passe: Yoto1975@\n";
        echo "👤 Nom: Super Admin\n";
        echo "🎯 Rôle: super_admin\n";
    }
    
    echo "\n=== Informations de connexion ===\n";
    echo "🌐 Frontend: http://localhost:8080\n";
    echo "🔧 Backend API: http://localhost:8000/api\n";
    echo "⚙️ Admin Filament: http://localhost:8000/admin\n";
    echo "\n🎉 NetNote est prêt à être utilisé !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
