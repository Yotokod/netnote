@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-primary via-green-800 to-green-900">
    <!-- Navigation -->
    <nav class="relative z-10 bg-white/10 backdrop-blur-sm border-b border-white/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-white">NetNote</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#features" class="text-white hover:text-accent transition-colors">Fonctionnalités</a>
                    <a href="#pricing" class="text-white hover:text-accent transition-colors">Tarifs</a>
                    <a href="{{ route('school.registration') }}" class="bg-accent hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Inscrire mon école
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl sm:text-6xl font-bold text-white mb-6">
                    Gérez votre école <br>
                    <span class="text-accent">en toute simplicité</span>
                </h1>
                <p class="text-xl text-white/80 mb-8 max-w-3xl mx-auto">
                    NetNote est la plateforme SaaS multi-tenant qui révolutionne la gestion scolaire en Afrique. 
                    Bulletins, notes, paiements, emplois du temps... tout en un !
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('school.registration') }}" class="bg-accent hover:bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors">
                        Commencer gratuitement
                    </a>
                    <a href="#demo" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-4 rounded-lg font-semibold text-lg transition-colors">
                        Voir la démo
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-white/10 backdrop-blur-sm py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div>
                        <div class="text-4xl font-bold text-accent">{{ $stats['schools_count'] }}+</div>
                        <div class="text-white/80">Écoles inscrites</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-accent">{{ $stats['students_count'] }}+</div>
                        <div class="text-white/80">Élèves gérés</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-accent">{{ $stats['countries_count'] }}+</div>
                        <div class="text-white/80">Pays couverts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section id="features" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Tout ce dont votre école a besoin
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Une suite complète d'outils conçus spécifiquement pour les établissements scolaires africains
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Gestion des élèves -->
            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Gestion des élèves</h3>
                <p class="text-gray-600">
                    Inscriptions, profils complets, suivi des paiements et historique scolaire centralisé
                </p>
            </div>

            <!-- Notes et bulletins -->
            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Notes & Bulletins</h3>
                <p class="text-gray-600">
                    Saisie rapide des notes, calcul automatique des moyennes et génération de bulletins personnalisés
                </p>
            </div>

            <!-- Paiements -->
            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Paiements intégrés</h3>
                <p class="text-gray-600">
                    FedaPay et KkiaPay intégrés pour les paiements en ligne avec rappels automatiques
                </p>
            </div>

            <!-- Emplois du temps -->
            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Emplois du temps</h3>
                <p class="text-gray-600">
                    Planification intelligente des cours avec gestion des conflits et export PDF
                </p>
            </div>

            <!-- Notifications -->
            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM8.5 17v-5a6 6 0 1112 0v5"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Notifications</h3>
                <p class="text-gray-600">
                    SMS, WhatsApp et email pour tenir informés parents, élèves et professeurs
                </p>
            </div>

            <!-- Multi-tenant -->
            <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 bg-accent rounded-lg flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Votre domaine</h3>
                <p class="text-gray-600">
                    Chaque école dispose de son propre sous-domaine personnalisable
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-primary py-24">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
            Prêt à moderniser votre école ?
        </h2>
        <p class="text-xl text-white/80 mb-8">
            Rejoignez les centaines d'écoles qui font déjà confiance à NetNote
        </p>
        <a href="{{ route('school.registration') }}" class="bg-accent hover:bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold text-lg transition-colors">
            Commencer maintenant
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4">NetNote</h3>
                <p class="text-gray-400">
                    La plateforme de gestion scolaire pensée pour l'Afrique
                </p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Produit</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Fonctionnalités</a></li>
                    <li><a href="#" class="hover:text-white">Tarifs</a></li>
                    <li><a href="#" class="hover:text-white">Sécurité</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Support</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Documentation</a></li>
                    <li><a href="#" class="hover:text-white">Contact</a></li>
                    <li><a href="#" class="hover:text-white">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Légal</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white">Confidentialité</a></li>
                    <li><a href="#" class="hover:text-white">CGU</a></li>
                    <li><a href="#" class="hover:text-white">Cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 NetNote. Tous droits réservés.</p>
        </div>
    </div>
</footer>
@endsection