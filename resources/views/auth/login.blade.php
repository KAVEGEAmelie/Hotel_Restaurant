<x-guest-layout>
    <!-- Votre Logo -->
    <div class="text-center mb-6">
        <a href="/">
            <img src="{{ asset('assets/img/logo.jpg') }}" alt="Logo Hôtel Le Printemps" class="w-24 h-24 mx-auto rounded-full border-4 border-white shadow-lg">
        </a>
        <h1 class="text-2xl font-bold text-white mt-4 font-serif">Accès Administration</h1>
        <p class="text-gray-300">Veuillez vous connecter pour continuer</p>
    </div>

    <!-- La carte du formulaire -->
    <div class="w-full px-8 py-8 bg-white/90 backdrop-blur-sm shadow-2xl rounded-2xl">
        
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" value="Adresse Email" class="text-gray-700" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Mot de passe -->
            <div class="mt-4">
                <x-input-label for="password" value="Mot de passe" class="text-gray-700" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Se souvenir de moi -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-700 shadow-sm focus:ring-green-600" name="remember">
                    <span class="ms-2 text-sm text-gray-600">Se souvenir de moi</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>

                <x-primary-button>
                    {{ __('Se connecter') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>