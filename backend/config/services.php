<?php

return [
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openrouter' => [
        'chave' => env('OPENROUTER_API_KEY'),
        'url_base' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
        'modelo' => env('OPENROUTER_MODEL', 'google/gemma-4-31b-it:free'),
        'modelos_reserva' => array_filter(array_map('trim', explode(',', env('OPENROUTER_FALLBACK_MODELS', 'nvidia/nemotron-3-super-120b-a12b:free,openrouter/free')))),
        'tempo_limite' => (int) env('OPENROUTER_TIMEOUT', 45),
        'certificados_ca' => env('OPENROUTER_CA_BUNDLE'),
        'url_app' => env('FRONTEND_URL', 'http://localhost:5173'),
        'nome_app' => env('APP_NAME', 'Reembolso de Viagens'),
    ],
];
