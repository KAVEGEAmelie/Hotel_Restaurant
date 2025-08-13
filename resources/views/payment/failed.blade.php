@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-times-circle me-2"></i> Paiement échoué</h5>
                </div>

                <div class="card-body text-center">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4>Votre paiement n'a pas abouti</h4>
                        <p>Veuillez réessayer ou contacter le support.</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('payment.initiate', $reservation->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-redo me-1"></i> Réessayer le paiement
                        </a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-headset me-1"></i> Support client
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
