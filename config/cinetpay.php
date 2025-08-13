<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CinetPay Configuration
    |--------------------------------------------------------------------------
    |
    | Cette configuration permet d'intégrer le service de paiement CinetPay
    | dans votre application Laravel.
    |
    */

    'api_key' => env('CINETPAY_API_KEY', ''),
    'site_id' => env('CINETPAY_SITE_ID', ''),
    'secret_key' => env('CINETPAY_SECRET_KEY', ''),
    'api_password' => env('CINETPAY_API_PASSWORD', ''),

    // Devise par défaut (XOF pour les pays francophones)
    'currency' => env('CINETPAY_CURRENCY', 'XOF'),

    // Environnement (sandbox ou production)
    'environment' => env('CINETPAY_ENVIRONMENT', 'sandbox'),

    // URLs de l'API
    'api_url' => env('CINETPAY_API_URL', 'https://api-checkout.cinetpay.com/v2'),
    'api_url_sandbox' => env('CINETPAY_API_URL_SANDBOX', 'https://sandbox.cinetpay.com/v2'),

    // URLs de callback
    'webhook_url' => env('CINETPAY_WEBHOOK_URL', ''),
    'return_url' => env('APP_URL') . '/payment/return',

    // Configuration des canaux de paiement
    'channels' => [
        'default' => 'ALL', // Tous les canaux
        'available' => [
            'ALL' => 'Tous les moyens',
            'MOBILE_MONEY' => 'Mobile Money',
            'CREDIT_CARD' => 'Carte Bancaire',
            'WALLET' => 'Portefeuille'
        ]
    ],

    // Paramètres spécifiques Mobile Money
    'mobile_money' => [
        'operators' => [
            'TG' => ['FLOOZ', 'TMONEY'], // Pour le Togo
            'CI' => ['ORANGE', 'MTN', 'MOOV'], // Pour la Côte d'Ivoire
            // Ajoutez d'autres pays au besoin
        ]
    ],

    // Paramètres de sécurité
    'security' => [
        'verify_ssl' => true, // Vérifier les certificats SSL
        'timeout' => 30, // Timeout en secondes pour les requêtes API
    ],

    // Logging
    'logging' => [
        'enabled' => env('APP_DEBUG', false),
        'channel' => env('LOG_CHANNEL', 'stack')
    ],

    // Personnalisation de l'interface
    'ui' => [
        'theme_color' => '#2C3E50', // Couleur principale
        'button_text' => 'Payer maintenant', // Texte du bouton
        'display_mode' => 'SAME_WINDOW' // REDIRECT, SAME_WINDOW, MODAL
    ]
];
