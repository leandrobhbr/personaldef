<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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

    // API da Anthropic (Claude) — "Fichas com IA" no app do personal (mesma conta do bhnaweb/espacotae). Preços em US$
    // por milhão de tokens, só para estimar o custo de cada sugestão no log (o valor cobrado é o da fatura da Anthropic)
    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
        'model' => env('ANTHROPIC_MODEL', 'claude-haiku-5-5'),
        'effort' => env('ANTHROPIC_EFFORT', 'medium'),
        'timeout' => (int) env('ANTHROPIC_TIMEOUT', 120),
        'preco_entrada_mtok' => (float) env('ANTHROPIC_PRECO_ENTRADA_MTOK', 0.10),
        'preco_saida_mtok' => (float) env('ANTHROPIC_PRECO_SAIDA_MTOK', 0.50),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
