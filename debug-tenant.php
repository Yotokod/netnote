<?php

/**
 * Script de débogage pour identifier le problème de tenant
 * Exécutez avec : php debug-tenant.php
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

echo "🔍 Débogage du système multi-tenant...\n\n";

try {
    // 1. Vérifier si la classe Tenant existe
    if (class_exists('App\Models\Tenant')) {
        echo "✅ Classe App\Models\Tenant trouvée\n";
    } else {
        echo "❌ Classe App\Models\Tenant non trouvée\n";
    }

    // 2. Vérifier si le service provider est chargé
    $providers = $app->getLoadedProviders();
    if (isset($providers['Spatie\Multitenancy\MultitenancyServiceProvider'])) {
        echo "✅ MultitenancyServiceProvider chargé\n";
    } else {
        echo "❌ MultitenancyServiceProvider non chargé\n";
    }

    // 3. Vérifier la configuration
    $config = $app['config'];
    if ($config->has('multitenancy')) {
        echo "✅ Configuration multitenancy présente\n";
        echo "   - Tenant model: " . $config->get('multitenancy.tenant_model') . "\n";
        echo "   - Tenant finder: " . $config->get('multitenancy.tenant_finder') . "\n";
    } else {
        echo "❌ Configuration multitenancy manquante\n";
    }

    // 4. Vérifier la base de données
    try {
        $pdo = $app['db']->connection()->getPdo();
        echo "✅ Connexion base de données OK\n";
        
        // Vérifier si la table tenants existe
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tenants'");
        if ($stmt->fetch()) {
            echo "✅ Table 'tenants' existe\n";
        } else {
            echo "❌ Table 'tenants' manquante\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur base de données: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 Recommandations :\n";
echo "1. Vérifiez que toutes les migrations ont été exécutées\n";
echo "2. Videz le cache : php artisan config:clear && php artisan cache:clear\n";
echo "3. Rechargez l'autoloader : composer dump-autoload\n";
echo "4. Vérifiez les permissions des fichiers\n";