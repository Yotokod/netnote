@extends('landing.layout')

@section('title', 'NetNote - Plateforme SaaS de Gestion Scolaire')
@section('description', 'NetNote est une plateforme SaaS multi-tenant de gestion scolaire destinée aux établissements d\'enseignement en Afrique. Gérez facilement vos élèves, professeurs, notes et finances.')

@section('content')
<!-- Hero Section -->
<section class="gradient-bg text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Révolutionnez la gestion de votre école
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                NetNote est la plateforme SaaS complète qui simplifie la gestion administrative, 
                pédagogique et financière de votre établissement scolaire.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#pricing" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Voir les tarifs
                </a>
                <a href="#features" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition duration-300">
                    Découvrir les fonctionnalités
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Fonctionnalités complètes
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Une suite complète d'outils pour gérer efficacement tous les aspects de votre établissement scolaire.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Gestion des élèves -->
            <div class="feature-card bg-gray-50 p-6 rounded-lg">
                <div class="text-indigo-600 text-4xl mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Gestion des élèves</h3>
                <p class="text-gray-600">
                    Inscriptions, dossiers complets, suivi des parents, historique académique et bien plus.
                </p>
            </div>

            <!-- Gestion des notes -->
            <div class="feature-card bg-gray-50 p-6 rounded-lg">
                <div class="text-indigo-600 text-4xl mb-4">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Notes et évaluations</h3>
                <p class="text-gray-600">
                    Saisie des notes, calculs automatiques, bulletins personnalisables et statistiques détaillées.
                </p>
            </div>

            <!-- Emploi du temps -->
            <div class="feature-card bg-gray-50 p-6 rounded-lg">
                <div class="text-indigo-600 text-4xl mb-4">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Emploi du temps</h3>
                <p class="text-gray-600">
                    Planification automatique, gestion des salles, conflits détectés et synchronisation mobile.
                </p>
            </div>

            <!-- Gestion financière -->
            <div class="feature-card bg-gray-50 p-6 rounded-lg">
                <div class="text-indigo-600 text-4xl mb-4">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Gestion financière</h3>
                <p class="text-gray-600">
                    Frais de scolarité, paiements, factures, rapports comptables et intégrations bancaires.
                </p>
            </div>

            <!-- Communication -->
            <div class="feature-card bg-gray-50 p-6 rounded-lg">
                <div class="text-indigo-600 text-4xl mb-4">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Communication</h3>
                <p class="text-gray-600">
                    SMS, emails automatiques, notifications push et portail parents intégré.
                </p>
            </div>

            <!-- Rapports et analyses -->
            <div class="feature-card bg-gray-50 p-6 rounded-lg">
                <div class="text-indigo-600 text-4xl mb-4">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Rapports et analyses</h3>
                <p class="text-gray-600">
                    Tableaux de bord interactifs, statistiques avancées et exports personnalisables.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Tarifs transparents
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Choisissez le plan qui correspond le mieux aux besoins de votre établissement.
            </p>
        </div>

        @if($plans->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-{{ min($plans->count(), 3) }} gap-8">
            @foreach($plans as $plan)
            <div class="bg-white rounded-lg shadow-lg p-8 {{ $loop->index === 1 ? 'ring-2 ring-indigo-600 relative' : '' }}">
                @if($loop->index === 1)
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                    <span class="bg-indigo-600 text-white px-4 py-1 rounded-full text-sm font-semibold">
                        Populaire
                    </span>
                </div>
                @endif
                
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $plan->name }}</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-gray-900">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                        <span class="text-gray-600">FCFA/{{ $plan->billing_cycle === 'monthly' ? 'mois' : 'an' }}</span>
                    </div>
                    <p class="text-gray-600 mb-8">{{ $plan->description }}</p>
                </div>

                <ul class="space-y-4 mb-8">
                    @if($plan->max_students)
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Jusqu'à {{ number_format($plan->max_students) }} élèves</span>
                    </li>
                    @else
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Élèves illimités</span>
                    </li>
                    @endif

                    @if($plan->max_teachers)
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Jusqu'à {{ number_format($plan->max_teachers) }} professeurs</span>
                    </li>
                    @else
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Professeurs illimités</span>
                    </li>
                    @endif

                    @if($plan->max_storage_gb)
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>{{ $plan->max_storage_gb }} GB de stockage</span>
                    </li>
                    @else
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Stockage illimité</span>
                    </li>
                    @endif

                    @if($plan->has_custom_domain)
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Domaine personnalisé</span>
                    </li>
                    @endif

                    @if($plan->has_advanced_reports)
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Rapports avancés</span>
                    </li>
                    @endif

                    @if($plan->has_priority_support)
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Support prioritaire</span>
                    </li>
                    @endif
                </ul>

                <button class="w-full {{ $loop->index === 1 ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-800 hover:bg-gray-900' }} text-white py-3 px-6 rounded-lg font-semibold transition duration-300">
                    Choisir ce plan
                </button>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center">
            <p class="text-gray-600">Aucun plan disponible pour le moment.</p>
        </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-indigo-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            Prêt à transformer votre école ?
        </h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-3xl mx-auto">
            Rejoignez des centaines d'établissements qui font déjà confiance à NetNote 
            pour gérer leur administration scolaire.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="#contact" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                Demander une démo
            </a>
            <a href="/super-admin" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition duration-300">
                Commencer maintenant
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Contactez-nous
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Notre équipe est là pour répondre à toutes vos questions et vous accompagner dans votre projet.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Informations de contact</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-envelope text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Email</p>
                            <p class="text-gray-600">contact@netnote.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-phone text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Téléphone</p>
                            <p class="text-gray-600">+225 XX XX XX XX</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-map-marker-alt text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Adresse</p>
                            <p class="text-gray-600">Abidjan, Côte d'Ivoire</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <form class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="school" class="block text-sm font-medium text-gray-700 mb-2">Nom de l'établissement</label>
                        <input type="text" id="school" name="school" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-600 focus:border-transparent"></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300">
                        Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection