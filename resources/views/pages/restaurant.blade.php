@extends('layouts.app')

@section('title', 'Restaurant - Hôtel Le Printemps')

@section('content')

<main id="main">

    <!-- ======= Section Titre de Page ======= -->
    <section class="page-title-section" style="background-image: url('{{ asset('assets/img/restaurant-bg.jpg') }}');">
        <div class="container text-center" data-aos="fade-up">
            <h1>Notre Restaurant</h1>
            <p class="text-white-50">Une invitation au voyage des saveurs</p>
        </div>
    </section>

    <!-- ======= Section Ambiance ======= -->
    <section id="ambiance" class="ambiance-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="{{ asset('assets/img/bar-ambiance.jpg') }}" class="img-fluid rounded-custom" alt="Ambiance chaleureuse du restaurant">
                </div>
                <div class="col-lg-6 ps-lg-5 mt-4 mt-lg-0" data-aos="fade-left">
                    <div class="section-title text-start">
                        <h2>L'Ambiance</h2>
                        <p>Chaleur & Convivialité</p>
                    </div>
                    <p class="fst-italic">
                        Notre salle de restaurant est un espace de partage où le bois noble rencontre un design moderne et confortable.
                    </p>
                    <p>
                        Que ce soit pour un dîner romantique, un repas d'affaires ou un moment en famille, notre cadre chaleureux et notre atmosphère "Zen" sont la promesse d'une expérience mémorable.
                    </p>
                    <div class="horaires">
                        <i class="bi bi-clock"></i>
                        <strong>Horaires d'ouverture :</strong> 12h00 - 15h00 | 19h00 - 22h00
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- ======= Section Menu Dynamique ======= -->
<section id="menu" class="menu-section py-5" style="background-color: #fcfbf7;">
    <div class="container" data-aos="fade-up">

        <div class="section-title">
            <h2>Notre Carte</h2>
            <p>Découvrez nos spécialités</p>
        </div>

        <!-- Navigation des Onglets -->
        <ul class="nav nav-tabs justify-content-center" id="menuTab" role="tablist" data-aos="fade-up" data-aos-delay="100">
            @foreach($categories as $categorie)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $categorie->id }}" data-bs-toggle="tab" data-bs-target="#content-{{ $categorie->id }}" type="button" role="tab" aria-controls="content-{{ $categorie->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        {{ $categorie->nom }}
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Contenu des Onglets -->
        <div class="tab-content" id="menuTabContent" data-aos="fade-up" data-aos-delay="200">
            @foreach($categories as $categorie)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="content-{{ $categorie->id }}" role="tabpanel" aria-labelledby="tab-{{ $categorie->id }}">
                    <div class="row gy-4 mt-2">
                        @forelse($categorie->plats as $plat)
                            <div class="col-lg-6 menu-item-v2">
                                {{-- <img src="{{ asset('storage/'.$plat->image) }}" class="menu-img" alt=""> --}}
                                <div class="menu-content">
                                    <h4>{{ $plat->nom }}</h4>
                                    <p class="ingredients">{{ $plat->description }}</p>
                                </div>
                                <div class="menu-prices">
                                    @if($plat->prix_simple)
                                        <div class="price-tag">
                                            <span>SIMPLE</span>
                                            <strong>{{ number_format($plat->prix_simple, 0, ',', ' ') }} F</strong>
                                        </div>
                                    @endif
                                    @if($plat->prix_menu)
                                         <div class="price-tag">
                                            <span>MENU</span>
                                            <strong>{{ number_format($plat->prix_menu, 0, ',', ' ') }} F</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Aucun plat dans cette catégorie pour le moment.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

    <!-- ======= Section Appel à l'Action (Réserver une table) ======= -->
    <section id="cta-booking-table" class="cta-booking-section">
        <div class="container" data-aos="fade-up">
            <div class="row">
                <div class="col-lg-9 text-center text-lg-start">
                    <h3>Prêt pour une expérience culinaire ?</h3>
                    <p>Réservez votre table dès maintenant pour garantir votre place et profiter d'un moment d'exception dans notre restaurant.</p>
                </div>
                <div class="col-lg-3 cta-btn-container text-center">
                    <a class="cta-btn align-middle" href="#">Réserver une table</a>
                </div>
            </div>
        </div>
    </section>

</main>

@endsection

@push('styles')
{{-- On ajoute le style spécifique à cette page --}}
<style>
    .rounded-custom {
        border-radius: 15px;
    }
    .ambiance-section .horaires {
        margin-top: 20px;
        font-size: 15px;
        font-weight: 500;
        color: var(--color-texte-principal);
    }
    .ambiance-section .horaires i {
        color: var(--color-vert-foret);
        margin-right: 8px;
    }

    .menu-section {
        padding: 80px 0;
    }
    .menu-section .menu-category {
        margin-bottom: 30px;
    }
    .menu-section .menu-category h4 {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--color-marron-titre);
        padding-bottom: 10px;
        border-bottom: 2px solid var(--color-dore-leger);
        margin-bottom: 20px;
    }
    .menu-section .menu-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .menu-section .item-name {
        font-weight: 600;
        font-size: 16px;
        font-family: 'Montserrat', sans-serif;
        color: var(--color-texte-principal);
    }
    .menu-section .item-price {
        font-weight: 700;
        color: var(--color-vert-foret);
    }
    .menu-section .item-description {
        font-size: 14px;
        color: #666;
        margin-top: 5px;
        font-style: italic;
    }

    /* Style pour les onglets du menu */
.menu-section .nav-tabs {
    border: 0;
}
.menu-section .nav-link {
    margin: 0 10px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: 600;
    color: var(--color-texte-principal);
    border: 0;
    border-bottom: 2px solid #eee;
    transition: 0.3s;
    border-radius: 0;
}
.menu-section .nav-link.active {
    color: var(--color-vert-foret);
    border-color: var(--color-vert-foret);
    background: transparent;
}
.menu-section .nav-link:hover {
    color: var(--color-vert-foret);
}

/* Style pour les items du menu (V2) */
.menu-item-v2 {
    display: flex;
    justify-content: space-between;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}
.menu-item-v2 .menu-content {
    flex-grow: 1;
}
.menu-item-v2 .menu-content h4 {
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 5px;
    color: var(--color-marron-titre);
}
.menu-item-v2 .menu-content .ingredients {
    font-style: italic;
    font-size: 14px;
    color: #555;
    margin: 0;
}
.menu-item-v2 .menu-prices {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-left: 20px;
}
.menu-item-v2 .price-tag {
    background: var(--color-vert-foret);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    text-align: center;
    margin-bottom: 5px;
    min-width: 120px;
}
.menu-item-v2 .price-tag span {
    display: block;
    font-size: 12px;
    text-transform: uppercase;
    opacity: 0.8;
}
.menu-item-v2 .price-tag strong {
    font-size: 16px;
}
</style>
@endpush