<?php

use App\Models\PushAssinatura;
use App\Support\LinkMagico;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Link mágico do Denilson (perfil personal) para liberar o aplicativo no celular dele
Artisan::command('app:link-personal', function () {
    ['link' => $link, 'codigo' => $codigo] = LinkMagico::gerar(null, 'PER');
    $this->info('Link (válido por '.LinkMagico::VALIDADE_DIAS." dias, um aparelho): {$link}");
    $this->info("Ou digite no app instalado o código: {$codigo}");
})->purpose('Gera o link mágico de acesso do personal ao aplicativo');

// Chaves VAPID do Web Push (rodar uma vez e copiar para o .env)
Artisan::command('app:chaves-push', function () {
    $chaves = VAPID::createVapidKeys();
    $this->line('VAPID_PUBLIC_KEY='.$chaves['publicKey']);
    $this->line('VAPID_PRIVATE_KEY='.$chaves['privateKey']);
})->purpose('Gera as chaves VAPID para os avisos do aplicativo');

// Avisa todos os aparelhos inscritos que há uma nova versão (rodar depois do deploy do app)
Artisan::command('app:avisar-versao {mensagem=Nova versão do aplicativo disponível. Toque para atualizar.}', function (string $mensagem) {
    $vapid = config('aplicativo.vapid');
    if (! $vapid['public_key'] || ! $vapid['private_key']) {
        $this->error('Defina VAPID_PUBLIC_KEY e VAPID_PRIVATE_KEY no .env (php artisan app:chaves-push).');

        return 1;
    }
    $webPush = new WebPush(['VAPID' => ['subject' => $vapid['subject'], 'publicKey' => $vapid['public_key'], 'privateKey' => $vapid['private_key']]]);
    $carga = json_encode(['title' => 'Personal DEF', 'body' => $mensagem, 'url' => rtrim(config('aplicativo.url'), '/').'/#/'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    PushAssinatura::chunkById(200, function ($assinaturas) use ($webPush, $carga) {
        foreach ($assinaturas as $a) {
            $webPush->queueNotification(Subscription::create(['endpoint' => $a->pus_endpoint, 'keys' => ['p256dh' => $a->pus_p256dh, 'auth' => $a->pus_auth]]), $carga);
        }
    }, 'pus_id');

    $enviados = 0;
    foreach ($webPush->flush() as $relatorio) {
        if ($relatorio->isSuccess()) {
            $enviados++;
        } elseif ($relatorio->isSubscriptionExpired()) {
            PushAssinatura::where('pus_endpoint', $relatorio->getEndpoint())->delete();
        }
    }
    $this->info("Aviso enviado para {$enviados} aparelho(s).");
})->purpose('Envia o aviso de nova versão do aplicativo por Web Push');
