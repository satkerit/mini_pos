<?php

return [
    'gateways' => [
        'qris' => [
            'name' => 'QRIS',
            'class' => App\Services\Gateways\QrisGateway::class,
        ],
        'dummy' => [
            'name' => 'Dummy Gateway',
            'class' => App\Services\Gateways\DummyGateway::class,
        ],
    ],
    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'qris'),
    'callback_url' => env('APP_URL') . '/api/payments/callback',
    'currency' => 'IDR',
];