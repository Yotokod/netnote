<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'NetNote - Plateforme SaaS de Gestion Scolaire')</title>
    <meta name="description" content="@yield('description', 'NetNote est une plateforme SaaS multi-tenant de gestion scolaire destinée aux établissements d\'enseignement en Afrique.')">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-indigo-600">NetNote</h1>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Fonctionnalités</a>
                    <a href="#pricing" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Tarifs</a>
                    <a href="#contact" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    <a href="/super-admin" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">Connexion</a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="mobile-menu-button text-gray-700 hover:text-indigo-600 focus:outline-none focus:text-indigo-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="mobile-menu hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t">
                <a href="#features" class="block text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">Fonctionnalités</a>
                <a href="#pricing" class="block text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">Tarifs</a>
                <a href="#contact" class="block text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-base font-medium">Contact</a>
                <a href="/super-admin" class="block bg-indigo-600 text-white px-3 py-2 rounded-md text-base font-medium hover:bg-indigo-700">Connexion</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4">NetNote</h3>
                    <p class="text-gray-300 mb-4">
                        Plateforme SaaS de gestion scolaire moderne et intuitive, 
                        conçue spécialement pour les établissements d'enseignement africains.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liens rapides</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-300 hover:text-white">Fonctionnalités</a></li>
                        <li><a href="#pricing" class="text-gray-300 hover:text-white">Tarifs</a></li>
                        <li><a href="#contact" class="text-gray-300 hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><i class="fas fa-envelope mr-2"></i> contact@netnote.com</li>
                        <li><i class="fas fa-phone mr-2"></i> +225 XX XX XX XX</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Abidjan, Côte d'Ivoire</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-300">&copy; {{ date('Y') }} NetNote. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        });
        
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>