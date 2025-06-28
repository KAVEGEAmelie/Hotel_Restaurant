@extends('layouts.admin')

@section('title', isset($chambre) ? 'Modifier la Chambre' : 'Ajouter une Chambre')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">
            {{ isset($chambre) ? 'Modifier : ' . $chambre->nom : 'Ajouter une nouvelle chambre' }}
        </h1>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Oups !</strong>
                    <span class="block sm:inline">Veuillez corriger les erreurs ci-dessous.</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="list-disc ml-4">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($chambre) ? route('admin.chambres.update', $chambre) : route('admin.chambres.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @if(isset($chambre))
                    @method('PUT')
                @endif

                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom de la chambre</label>
                    <input type="text" name="nom" id="nom" value="{{ old('nom', $chambre->nom ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="prix_par_nuit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix / nuit (FCFA)</label>
                        <input type="number" step="500" name="prix_par_nuit" id="prix_par_nuit" value="{{ old('prix_par_nuit', $chambre->prix_par_nuit ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="capacite" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacité (personnes)</label>
                        <input type="number" name="capacite" id="capacite" value="{{ old('capacite', $chambre->capacite ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="description_courte" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description Courte</label>
                    <input type="text" name="description_courte" id="description_courte" value="{{ old('description_courte', $chambre->description_courte ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Une phrase d'accroche pour la liste des chambres.">
                </div>
                <div>
                    <label for="description_longue" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description Détaillée</label>
                    <textarea name="description_longue" id="description_longue" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Détaillez les équipements, l'ambiance, etc.">{{ old('description_longue', $chambre->description_longue ?? '') }}</textarea>
                </div>

                <div>
                    <label for="image_principale" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image Principale</label>
                    <input type="file" name="image_principale" id="image_principale" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @if(isset($chambre) && $chambre->image_principale)
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">Image actuelle :</p>
                            <img src="{{ asset('storage/' . $chambre->image_principale) }}" alt="Image actuelle" class="mt-2 h-32 w-auto rounded-md">
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.chambres.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Annuler</a>
                    <button type="submit" class="ml-4 px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-md">
                        {{ isset($chambre) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection