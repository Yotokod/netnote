<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetNote - Digitalisez votre École</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float1 {
            0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
            25% { transform: translateY(-15px) translateX(10px) rotate(2deg); }
            50% { transform: translateY(-25px) translateX(0px) rotate(0deg); }
            75% { transform: translateY(-15px) translateX(-10px) rotate(-2deg); }
        }
        @keyframes float2 {
            0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
            33% { transform: translateY(-20px) translateX(-15px) rotate(-3deg); }
            66% { transform: translateY(-10px) translateX(15px) rotate(3deg); }
        }
        @keyframes scrollLeft {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-fade-in-up { animation: fadeInUp 1s ease-out; }
        .animate-float-1 { animation: float1 4s ease-in-out infinite; }
        .animate-float-2 { animation: float2 5s ease-in-out infinite; }
        .animate-pulse-slow { animation: pulse 2s ease-in-out infinite; }
        .testimonials-scroll { animation: scrollLeft 30s linear infinite; }
        .testimonials-scroll:hover { animation-play-state: paused; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-200">
    
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 backdrop-blur-md bg-white/80 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <a href="/" class="flex items-center space-x-3">
                    <img src="{{ asset('images/NetNote.png') }}" alt="NetNote" class="h-10">
                    <span class="text-xl font-bold text-gray-900">NetNote</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-gray-900 transition">
                        <i class="fas fa-star mr-2"></i>Fonctionnalités
                    </a>
                    <a href="#pricing" class="text-gray-700 hover:text-gray-900 transition">
                        <i class="fas fa-tags mr-2"></i>Tarifs
                    </a>
                    <a href="#about" class="text-gray-700 hover:text-gray-900 transition">
                        <i class="fas fa-info-circle mr-2"></i>À propos
                    </a>
                    <a href="#contact" class="text-gray-700 hover:text-gray-900 transition">
                        <i class="fas fa-envelope mr-2"></i>Contact
                    </a>
                    <a href="{{ route('school.registration') }}" class="text-gray-700 hover:text-gray-900 transition">
                        <i class="fas fa-school mr-2"></i>Inscription
                    </a>
                </div>

                <div class="hidden md:block">
                    <a href="/super-admin" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full font-semibold hover:shadow-lg transition transform hover:scale-105">
                        Se connecter
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button class="md:hidden text-gray-700" id="mobile-menu-btn">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="#features" class="block py-2 text-gray-700">Fonctionnalités</a>
                <a href="#pricing" class="block py-2 text-gray-700">Tarifs</a>
                <a href="#about" class="block py-2 text-gray-700">À propos</a>
                <a href="#contact" class="block py-2 text-gray-700">Contact</a>
                <a href="{{ route('school.registration') }}" class="block py-2 text-gray-700">Inscription</a>
                <a href="/super-admin" class="block py-2 mt-2 text-center px-4 py-2 bg-green-500 text-white rounded-full">Se connecter</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4 relative overflow-hidden">
        <div class="container mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="animate-fade-in-up">
                    <div class="inline-block bg-gray-900 text-white px-4 py-2 rounded-full mb-4 animate-pulse-slow">
                        <i class="fas fa-bolt mr-2"></i>Innovation Éducative
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        Digitalisez votre <span class="text-blue-600 relative">École<span class="absolute bottom-0 left-0 w-full h-1 bg-blue-600"></span></span> en quelques clics
                    </h1>
                    <p class="text-lg text-gray-600 mb-6">
                        NetNote transforme la gestion scolaire avec des applications mobiles personnalisées et un calcul automatique des moyennes.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-gray-900 hover:shadow-xl transition">
                            <div class="text-3xl font-bold text-gray-900">{{ $stats['schools_count'] ?? 5 }}+</div>
                            <div class="text-sm text-gray-600">Établissements actifs</div>
                        </div>
                        <div class="bg-white p-4 rounded-2xl shadow-md border-l-4 border-gray-900 hover:shadow-xl transition">
                            <div class="text-3xl font-bold text-gray-900">24%</div>
                            <div class="text-sm text-gray-600">Gain en productivité</div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('school.registration') }}" class="px-6 py-3 bg-gray-900 text-white rounded-full font-semibold hover:bg-gray-800 transition transform hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i>Commencer
                        </a>
                        <button class="px-6 py-3 border-2 border-gray-900 text-gray-900 rounded-full font-semibold hover:bg-gray-900 hover:text-white transition">
                            <i class="fas fa-play-circle mr-2"></i>Démo
                        </button>
                    </div>
                </div>

                <div class="hidden md:block relative h-96">
                    <div class="absolute top-5 left-5 bg-white p-4 rounded-2xl shadow-xl animate-float-1">
                        <i class="fas fa-mobile-alt text-2xl text-gray-900"></i>
                        <p class="mt-2 text-sm font-bold">App Mobile</p>
                    </div>
                    <div class="absolute top-0 right-5 bg-white p-4 rounded-2xl shadow-xl animate-float-2">
                        <i class="fas fa-calculator text-2xl text-gray-900"></i>
                        <p class="mt-2 text-sm font-bold">Calcul Auto</p>
                    </div>
                    <div class="absolute bottom-24 left-1/3 bg-white p-4 rounded-2xl shadow-xl animate-float-1" style="animation-delay: 0.5s">
                        <i class="fas fa-chart-pie text-2xl text-gray-900"></i>
                        <p class="mt-2 text-sm font-bold">Analytics</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Schools Section -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <!-- Filters -->
            <div class="bg-white rounded-3xl shadow-lg p-6 mb-8">
                <div class="grid md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Pays</label>
                        <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-gray-900 focus:outline-none">
                            <option>Tous les pays</option>
                            <option>Cameroun</option>
                            <option>Sénégal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Ville</label>
                        <select class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-gray-900 focus:outline-none">
                            <option>Toutes les villes</option>
                            <option>Douala</option>
                            <option>Yaoundé</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Rechercher</label>
                        <input type="text" placeholder="Nom de l'école..." class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-gray-900 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Schools Grid -->
            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    ['name' => 'Collège Bilingue Les Leaders', 'city' => 'Douala', 'type' => 'Secondaire'],
                    ['name' => 'École Primaire Saint-Joseph', 'city' => 'Yaoundé', 'type' => 'Primaire'],
                    ['name' => 'Complexe Scolaire La Référence', 'city' => 'Bafoussam', 'type' => 'Mixte']
                ] as $school)
                <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition transform hover:-translate-y-2 cursor-pointer">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 relative">
                        <span class="absolute top-4 left-4 bg-gray-900 text-white px-3 py-1 rounded-full text-xs">{{ $school['type'] }}</span>
                    </div>
                    <div class="p-6">
                        <h5 class="text-xl font-bold text-gray-900 mb-2">{{ $school['name'] }}</h5>
                        <div class="flex items-center text-gray-600 mb-4">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>{{ $school['city'] }}, Cameroun</span>
                        </div>
                        <button class="w-full py-3 bg-gray-900 text-white rounded-xl font-semibold hover:bg-gray-800 transition">
                            Visiter la page <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gradient-to-br from-blue-100 to-blue-200">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-semibold">Fonctionnalités</span>
                <h2 class="text-4xl font-bold text-gray-900 mt-4 mb-3">Une plateforme "All in One"</h2>
                <p class="text-lg text-gray-600">Des outils puissants pour révolutionner la gestion scolaire</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    ['icon' => 'fa-mobile-alt', 'title' => 'Application Mobile', 'desc' => 'Dashboard personnalisable pour chaque école'],
                    ['icon' => 'fa-calculator', 'title' => 'Calcul Automatique', 'desc' => 'Moyennes et bulletins générés instantanément'],
                    ['icon' => 'fa-users', 'title' => 'Gestion Élèves', 'desc' => 'Base de données complète et sécurisée'],
                    ['icon' => 'fa-bell', 'title' => 'Notifications', 'desc' => 'SMS et push pour parents et élèves'],
                    ['icon' => 'fa-chart-bar', 'title' => 'Statistiques', 'desc' => 'Tableaux de bord et analyses avancées'],
                    ['icon' => 'fa-shield-alt', 'title' => 'Sécurité', 'desc' => 'Données chiffrées et accès sécurisés']
                ] as $feature)
                <div class="bg-white p-8 rounded-3xl shadow-md hover:shadow-xl transition transform hover:-translate-y-2 text-center">
                    <div class="w-20 h-20 bg-gray-900 text-white rounded-full flex items-center justify-center mx-auto mb-4 transition transform hover:rotate-360">
                        <i class="fas {{ $feature['icon'] }} text-3xl"></i>
                    </div>
                    <h5 class="text-xl font-bold text-gray-900 mb-2">{{ $feature['title'] }}</h5>
                    <p class="text-gray-600 text-sm">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-semibold">Tarifs</span>
                <h2 class="text-4xl font-bold text-gray-900 mt-4 mb-3">Choisissez votre formule</h2>
                <p class="text-lg text-gray-600">Des solutions adaptées à chaque besoin et budget</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @if($plans->count() > 0)
                    @foreach($plans as $plan)
                    <div class="bg-white border-2 border-gray-200 rounded-3xl p-8 hover:border-gray-900 transition hover:shadow-xl {{ $loop->index === 1 ? 'bg-gray-900 text-white transform scale-105 relative shadow-2xl' : '' }}">
                        @if($loop->index === 1)
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-white text-gray-900 px-6 py-2 rounded-full text-xs font-bold">
                            POPULAIRE
                        </div>
                        @endif
                        <h4 class="text-2xl font-bold {{ $loop->index === 1 ? 'text-white' : 'text-gray-900' }} mb-4">{{ $plan->name }}</h4>
                        <div class="text-4xl font-bold {{ $loop->index === 1 ? 'text-white' : 'text-gray-900' }} mb-2">
                            {{ number_format($plan->price, 0, ',', ' ') }}<span class="text-lg {{ $loop->index === 1 ? 'text-gray-300' : 'text-gray-600' }} ml-2">FCFA</span>
                        </div>
                        <p class="{{ $loop->index === 1 ? 'text-gray-300' : 'text-gray-600' }} mb-6">{{ $plan->description }}</p>
                        <ul class="space-y-3 mb-8">
                            @if($plan->max_students)
                            <li class="flex items-center"><i class="fas fa-check {{ $loop->index === 1 ? 'text-green-400' : 'text-green-500' }} mr-3"></i>Jusqu'à {{ number_format($plan->max_students) }} élèves</li>
                            @else
                            <li class="flex items-center"><i class="fas fa-check {{ $loop->index === 1 ? 'text-green-400' : 'text-green-500' }} mr-3"></i>Élèves illimités</li>
                            @endif
                            @if($plan->has_custom_domain)
                            <li class="flex items-center"><i class="fas fa-check {{ $loop->index === 1 ? 'text-green-400' : 'text-green-500' }} mr-3"></i>Domaine personnalisé</li>
                            @endif
                            @if($plan->has_advanced_reports)
                            <li class="flex items-center"><i class="fas fa-check {{ $loop->index === 1 ? 'text-green-400' : 'text-green-500' }} mr-3"></i>Rapports avancés</li>
                            @endif
                            @if($plan->has_priority_support)
                            <li class="flex items-center"><i class="fas fa-check {{ $loop->index === 1 ? 'text-green-400' : 'text-green-500' }} mr-3"></i>Support prioritaire 24/7</li>
                            @endif
                        </ul>
                        <button class="w-full py-3 {{ $loop->index === 1 ? 'bg-white text-gray-900 hover:bg-gray-100' : 'border-2 border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white' }} rounded-full font-semibold transition">
                            {{ $loop->index === 1 ? 'Demander un devis' : 'Commencer maintenant' }}
                        </button>
                    </div>
                    @endforeach
                @else
                <!-- Fallback pricing -->
                <div class="bg-white border-2 border-gray-200 rounded-3xl p-8 hover:border-gray-900 transition hover:shadow-xl">
                    <h4 class="text-2xl font-bold text-gray-900 mb-4">Version Free</h4>
                    <div class="text-5xl font-bold text-gray-900 mb-2">0<span class="text-lg text-gray-600 ml-2">FCFA</span></div>
                    <p class="text-gray-600 mb-6">Pour les petites écoles</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Gestion de base des élèves</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Calcul des moyennes</li>
                        <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Jusqu'à 100 élèves</li>
                        <li class="flex items-center opacity-40"><i class="fas fa-times text-gray-400 mr-3"></i>Application mobile</li>
                    </ul>
                    <button class="w-full py-3 border-2 border-gray-900 text-gray-900 rounded-full font-semibold hover:bg-gray-900 hover:text-white transition">
                        Commencer gratuitement
                    </button>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white overflow-hidden">
        <div class="container mx-auto px-4 mb-16">
            <div class="text-center">
                <span class="bg-gray-900 text-white px-4 py-2 rounded-full text-sm font-semibold">Témoignages</span>
                <h2 class="text-4xl font-bold text-gray-900 mt-4 mb-3">Ce qu'ils disent de NetNote</h2>
                <p class="text-lg text-gray-600">La satisfaction de nos utilisateurs est notre priorité</p>
            </div>
        </div>

        <!-- Scrolling Testimonials -->
        <div class="flex space-x-6 testimonials-scroll">
            @foreach([
                ['name' => 'Dr. Marie Kamga', 'role' => 'Directrice, Collège Les Leaders', 'text' => 'NetNote a révolutionné notre gestion scolaire. Le calcul automatique des moyennes nous fait gagner un temps précieux.'],
                ['name' => 'Paul Ngouo', 'role' => 'Parent d\'élève', 'text' => 'L\'application mobile pour les parents est fantastique ! Je peux suivre les notes de mes enfants en temps réel.'],
                ['name' => 'Sophie Tchuente', 'role' => 'Enseignante', 'text' => 'Interface claire et fonctionnelle. Le support technique est réactif et à l\'écoute.']
            ] as $testimonial)
            @for($i = 0; $i < 2; $i++)
            <div class="bg-white p-6 rounded-2xl shadow-md min-w-[400px] flex-shrink-0">
                <div class="flex text-yellow-400 mb-3">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-gray-600 mb-4">"{{ $testimonial['text'] }}"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                        {{ substr($testimonial['name'], 0, 2) }}
                    </div>
                    <div>
                        <h6 class="font-bold text-gray-900">{{ $testimonial['name'] }}</h6>
                        <p class="text-sm text-gray-600">{{ $testimonial['role'] }}</p>
                    </div>
                </div>
            </div>
            @endfor
            @endforeach
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contact" class="py-20 bg-gradient-to-r from-gray-900 to-gray-800 text-white relative overflow-hidden">
        <div class="container mx-auto px-4 text-center relative z-10">
            <h3 class="text-4xl font-bold mb-4">Prêt à digitaliser votre école ?</h3>
            <p class="text-xl text-gray-300 mb-8">Rejoignez les établissements qui ont fait le choix de l'innovation.</p>
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <a href="{{ route('school.registration') }}" class="px-8 py-4 bg-white text-gray-900 rounded-full font-bold hover:bg-gray-100 transition transform hover:scale-105">
                    <i class="fas fa-rocket mr-2"></i>Démarrer maintenant
                </a>
                <button class="px-8 py-4 border-2 border-white text-white rounded-full font-bold hover:bg-white hover:text-gray-900 transition">
                    <i class="fas fa-phone mr-2"></i>Nous contacter
                </button>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-3xl mx-auto">
                <div class="text-center">
                    <i class="fas fa-shield-alt text-4xl mb-3"></i>
                    <p class="font-bold">Sécurité garantie</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-headset text-4xl mb-3"></i>
                    <p class="font-bold">Support 24/7</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-sync-alt text-4xl mb-3"></i>
                    <p class="font-bold">Mises à jour régulières</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-gray-900 text-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8">
                <div class="flex justify-center space-x-6 mb-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">Company</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">About Us</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Team</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Products</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Blog</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Pricing</a>
                </div>
                <div class="flex justify-center space-x-6 mb-6">
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-dribbble text-2xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter text-2xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram text-2xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-pinterest text-2xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-github text-2xl"></i></a>
                </div>
                <p class="text-gray-400">Copyright © {{ date('Y') }} NetNote. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>