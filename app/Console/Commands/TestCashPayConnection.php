<?php

namespace App\Console\Commands;

use App\Services\CashPayService;
use Illuminate\Console\Command;

class TestCashPayConnection extends Command
{
    protected $signature = 'cashpay:test';

    protected $description = 'Test la connexion à l\'API CashPay';

    public function handle(CashPayService $cashPayService)
    {
        $this->info('🔍 Test de la connexion à l\'API CashPay...');

        if (!$cashPayService->isConfigured()) {
            $this->error('❌ Configuration CashPay incomplète');
            $this->line('');
            $this->line('Variables d\'environnement manquantes :');
            $this->line('- CASHPAY_API_URL');
            $this->line('- CASHPAY_USERNAME');
            $this->line('- CASHPAY_API_KEY');
            $this->line('- CASHPAY_API_REFERENCE');
            $this->line('');
            $this->line('Consultez le fichier CASHPAY_SETUP.md pour plus d\'informations.');
            return 1;
        }

        $this->info('✅ Configuration CashPay détectée');

        $result = $cashPayService->testConnection();

        if ($result['success']) {
            $this->info('✅ Connexion à l\'API CashPay réussie');
            $this->line('Status: ' . $result['status']);
            $this->line('Message: ' . $result['message']);
        } else {
            $this->error('❌ Échec de la connexion à l\'API CashPay');
            $this->line('Erreur: ' . ($result['error'] ?? 'Erreur inconnue'));
            return 1;
        }

        return 0;
    }
}
