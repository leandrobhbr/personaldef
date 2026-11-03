<?php

return [
    // Endereço do aplicativo (PWA) usado no link mágico
    'url' => env('APLICATIVO_URL', rtrim((string) env('APP_URL', 'http://localhost:8086'), '/').'/app'),

    // Web Push (avisos de nova versão). Gerar com: php artisan app:chaves-push
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'https://personaldef.com.br'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

    // Senha fixa da área do personal (tela "Sou o personal" do app)
    'senha_personal' => env('PERSONAL_SENHA'),

    'aviso_ficha_personalizada' => 'Deseja uma ficha personalizada? Acesse personaldef.com.br',
];
