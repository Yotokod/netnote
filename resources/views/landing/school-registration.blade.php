@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Inscrivez votre école</h1>
            <p class="text-lg text-gray-600">
                Rejoignez NetNote et modernisez la gestion de votre établissement
            </p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="px-8 py-6 bg-primary">
                <h2 class="text-xl font-semibold text-white">Informations de l'école</h2>
            </div>

            <form action="{{ route('school.registration.store') }}" method="POST" class="px-8 py-6 space-y-6">
                @csrf

                <!-- École Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de l'école *
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="founder" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom du fondateur *
                        </label>
                        <input type="text" name="founder" id="founder" required
                               value="{{ old('founder') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('founder') border-red-500 @enderror">
                        @error('founder')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="year_founded" class="block text-sm font-medium text-gray-700 mb-2">
                            Année de création *
                        </label>
                        <input type="number" name="year_founded" id="year_founded" required
                               min="1900" max="{{ date('Y') }}" value="{{ old('year_founded') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('year_founded') border-red-500 @enderror">
                        @error('year_founded')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Pays *
                        </label>
                        <select name="country_id" id="country_id" required x-data="{ selectedCountry: '{{ old('country_id') }}' }" x-model="selectedCountry"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('country_id') border-red-500 @enderror">
                            <option value="">Sélectionnez un pays</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="city_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Ville *
                        </label>
                        <select name="city_id" id="city_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('city_id') border-red-500 @enderror">
                            <option value="">Sélectionnez une ville</option>
                            @foreach($countries as $country)
                                @foreach($country->cities as $city)
                                    <option value="{{ $city->id }}" data-country="{{ $country->id }}" 
                                            {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('city_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quartier" class="block text-sm font-medium text-gray-700 mb-2">
                            Quartier *
                        </label>
                        <input type="text" name="quartier" id="quartier" required
                               value="{{ old('quartier') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('quartier') border-red-500 @enderror">
                        @error('quartier')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de contact</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email_pro" class="block text-sm font-medium text-gray-700 mb-2">
                                Email professionnel *
                            </label>
                            <input type="email" name="email_pro" id="email_pro" required
                                   value="{{ old('email_pro') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('email_pro') border-red-500 @enderror">
                            @error('email_pro')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone *
                            </label>
                            <input type="tel" name="phone" id="phone" required
                                   value="{{ old('phone') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- About -->
                <div>
                    <label for="about" class="block text-sm font-medium text-gray-700 mb-2">
                        À propos de l'école
                    </label>
                    <textarea name="about" id="about" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent @error('about') border-red-500 @enderror"
                              placeholder="Décrivez brièvement votre établissement...">{{ old('about') }}</textarea>
                    @error('about')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('landing.index') }}" class="text-gray-600 hover:text-gray-900">
                        ← Retour à l'accueil
                    </a>
                    <button type="submit" class="bg-accent hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold transition-colors">
                        Inscrire mon école
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');
    
    function updateCities() {
        const selectedCountry = countrySelect.value;
        const cityOptions = citySelect.querySelectorAll('option[data-country]');
        
        citySelect.value = '';
        
        cityOptions.forEach(option => {
            if (option.dataset.country === selectedCountry) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }
    
    countrySelect.addEventListener('change', updateCities);
    updateCities(); // Initialize on page load
});
</script>
@endpush
@endsection