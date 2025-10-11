<?php

/**
 * Script de correction pour le problème de multitenancy
 * Exécutez ce script avec : php fix-multitenancy.php
 */

echo "🔧 Correction du problème de multitenancy...\n";

// 1. Vérifier si le service provider de multitenancy est enregistré
$providersFile = 'bootstrap/providers.php';
$providers = include $providersFile;

$multitenancyProvider = 'Spatie\Multitenancy\MultitenancyServiceProvider';

if (!in_array($multitenancyProvider, $providers)) {
    echo "❌ Service provider manquant. Ajout en cours...\n";
    
    $newProviders = array_merge($providers, [$multitenancyProvider]);
    
    $content = "<?php\n\nreturn [\n";
    foreach ($newProviders as $provider) {
        $content .= "    {$provider}::class,\n";
    }
    $content .= "];\n";
    
    file_put_contents($providersFile, $content);
    echo "✅ Service provider ajouté.\n";
} else {
    echo "✅ Service provider déjà présent.\n";
}

// 2. Vérifier la configuration
echo "🔍 Vérification de la configuration...\n";

if (!file_exists('config/multitenancy.php')) {
    echo "❌ Fichier de configuration manquant.\n";
    echo "📝 Exécutez : php artisan vendor:publish --provider=\"Spatie\Multitenancy\MultitenancyServiceProvider\" --tag=\"multitenancy-config\"\n";
} else {
    echo "✅ Configuration présente.\n";
}

// 3. Vérifier les modèles
if (!file_exists('app/Models/Tenant.php')) {
    echo "❌ Modèle Tenant manquant.\n";
} else {
    echo "✅ Modèle Tenant présent.\n";
}

if (!file_exists('app/Multitenancy/DomainTenantFinder.php')) {
    echo "❌ DomainTenantFinder manquant.\n";
} else {
    echo "✅ DomainTenantFinder présent.\n";
}

echo "\n🎯 Actions recommandées :\n";
echo "1. php artisan config:clear\n";
echo "2. php artisan cache:clear\n";
echo "3. php artisan route:clear\n";
echo "4. composer dump-autoload\n";
echo "5. php artisan migrate\n";

echo "\n✨ Correction terminée !\n";