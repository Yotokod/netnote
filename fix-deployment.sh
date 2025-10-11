#!/bin/bash

echo "🚀 Correction du déploiement NetNote..."

# 1. Nettoyer le cache
echo "🧹 Nettoyage du cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Recharger l'autoloader
echo "🔄 Rechargement de l'autoloader..."
composer dump-autoload --optimize

# 3. Publier les configurations
echo "📝 Publication des configurations..."
php artisan vendor:publish --provider="Spatie\Multitenancy\MultitenancyServiceProvider" --tag="multitenancy-config" --force
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --force

# 4. Migrations
echo "🗄️ Exécution des migrations..."
php artisan migrate --force

# 5. Seeders
echo "🌱 Exécution des seeders..."
php artisan db:seed --class=SuperAdminSeeder --force
php artisan db:seed --class=PlansAndFeaturesSeeder --force
php artisan db:seed --class=SchoolSeeder --force

# 6. Permissions des fichiers
echo "🔐 Configuration des permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 7. Optimisation pour production
echo "⚡ Optimisation pour production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Correction terminée !"
echo "🌐 Votre application NetNote est prête !"