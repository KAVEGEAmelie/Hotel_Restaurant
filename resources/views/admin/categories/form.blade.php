@extends('layouts.admin')
@section('title', isset($categorie) ? 'Modifier' : 'Ajouter une Catégorie')
@section('content')
    {{-- ... (Structure similaire au formulaire des chambres) ... --}}
    <form action="{{ isset($categorie) ? route('admin.categories.update', $categorie) : route('admin.categories.store') }}" method="POST">
        {{-- ... Champs pour "Nom" (text) et "Ordre" (number) ... --}}
    </form>
@endsection