@extends('layouts.app')

@section('title', 'Simulation de Paiement CashPay')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white text-center">
            <h2 class="text-2xl font-bold">🧪 Mode Test CashPay</h2>
            <p class="text-blue-100 mt-2">Simulation de paiement</p>
        </div>

        <div class="p-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-yellow-800 font-medium">Mode Test Activé</span>
                </div>
                <p class="text-yellow-700 text-sm mt-1">
                    Cette page simule l'interface de paiement CashPay. 
                    Aucun paiement réel ne sera effectué.
                </p>
            </div>

            <div class="space-y-4">
                <!-- Informations de la facture -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-800 mb-3">Détails de la facture</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Facture:</span>
                            <span class="font-mono">{{ $invoice_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Montant:</span>
                            <span class="font-bold">{{ number_format($amount, 0, ',', ' ') }} {{ $currency }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Statut:</span>
                            <span class="text-orange-600 font-medium">En attente</span>
                        </div>
                    </div>
                </div>

                <!-- Simulation de paiement -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-blue-800 mb-3">Simulation de paiement</h3>
                    <p class="text-blue-700 text-sm mb-4">
                        Choisissez le résultat de votre test :
                    </p>
                    
                    <div class="space-y-3">
                        <button 
                            onclick="simulatePayment('success')" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simuler Paiement Réussi
                        </button>
                        
                        <button 
                            onclick="simulatePayment('failed')" 
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Simuler Paiement Échoué
                        </button>
                    </div>
                </div>

                <!-- Retour -->
                <div class="pt-4 border-t">
                    <a href="{{ route('home') }}" class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-4 rounded-lg transition duration-200">
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function simulatePayment(result) {
    const invoiceId = '{{ $invoice_id }}';
    
    if (result === 'success') {
        // Simuler un paiement réussi
        alert('🎉 Paiement simulé avec succès!\n\n' + 
              'Dans un vrai système, vous seriez redirigé vers la page de confirmation.');
        
        // Redirection vers une page de succès (à adapter selon votre logique)
        window.location.href = '{{ route("home") }}?payment=success&invoice=' + invoiceId;
        
    } else if (result === 'failed') {
        // Simuler un paiement échoué
        alert('❌ Paiement simulé comme échoué!\n\n' + 
              'Dans un vrai système, vous pourriez réessayer ou choisir un autre mode de paiement.');
        
        // Redirection vers une page d'échec (à adapter selon votre logique)
        window.location.href = '{{ route("home") }}?payment=failed&invoice=' + invoiceId;
    }
}

// Simulation du timer de session
let timeLeft = 300; // 5 minutes
const timer = setInterval(() => {
    timeLeft--;
    if (timeLeft <= 0) {
        clearInterval(timer);
        alert('⏰ Session de paiement expirée!\n\nDans un vrai système, cette session aurait expiré.');
    }
}, 1000);
</script>
@endsection
