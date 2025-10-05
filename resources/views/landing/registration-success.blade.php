@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12">
    <div class="max-w-md w-full">
        <!-- Success Card -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <!-- Success Icon -->
            <div class="bg-green-500 px-6 py-8 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Inscription réussie !</h1>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <div class="text-center mb-6">
                    <p class="text-gray-600 mb-4">
                        Félicitations ! Votre école a été enregistrée avec succès dans NetNote.
                    </p>
                    
                    @if(session('school'))
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h3 class="font-semibold text-gray-900 mb-2">{{ session('school')->name }}</h3>
                            <p class="text-sm text-gray-600">
                                <strong>Sous-domaine :</strong> 
                                <span class="text-accent font-medium">{{ session('school')->subdomain }}.netnote.local</span>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Next Steps -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-900">Prochaines étapes :</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-accent text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</div>
                            <p>Notre équipe va examiner votre demande sous 24h</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-accent text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</div>
                            <p>Vous recevrez un email avec vos identifiants d'accès</p>
                        </div>
                        <div class="flex items-start">
                            <div class="w-6 h-6 bg-accent text-white rounded-full flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</div>
                            <p>Configurez votre école et commencez à l'utiliser</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col space-y-3 mt-8">
                    <a href="mailto:support@netnote.com" class="bg-accent hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-center font-semibold transition-colors">
                        Contacter le support
                    </a>
                    <a href="{{ route('landing.index') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-center font-semibold transition-colors">
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>Des questions ? Contactez-nous à <a href="mailto:support@netnote.com" class="text-accent hover:underline">support@netnote.com</a></p>
        </div>
    </div>
</div>
@endsection