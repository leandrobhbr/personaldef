<?php

namespace App\Console\Commands;

use App\Services\FichaIaService;
use Illuminate\Console\Command;

/**
 * Testa o "Fichas com IA" com a API real: lê o pedido de um arquivo de texto e mostra as fichas sugeridas (nada é
 * gravado). Ex.: php artisan fichas:testar-ia storage/app/pedido.txt
 */
class TestarFichasIa extends Command
{
    protected $signature = 'fichas:testar-ia {arquivo : arquivo .txt com o pedido, como o personal escreveria}';

    protected $description = 'Manda um pedido de fichas para a IA (Claude) e mostra a sugestão, sem gravar nada';

    public function handle(FichaIaService $service): int
    {
        $arquivo = $this->argument('arquivo');
        if (! is_file($arquivo)) {
            $this->error("Arquivo não encontrado: {$arquivo}");

            return self::FAILURE;
        }

        $inicio = microtime(true);
        $r = $service->sugerir(file_get_contents($arquivo));
        $this->info($r['resumo']);
        foreach ($r['avisos'] as $aviso) {
            $this->warn("! {$aviso}");
        }
        foreach ($r['fichas'] as $f) {
            $this->newLine();
            $this->line("<options=bold>{$f['titulo']}</>".($f['orientacoes'] ? " — {$f['orientacoes']}" : ''));
            foreach ($f['exercicios'] as $i => $e) {
                $nome = $e['exercicio'] ? "{$e['exercicio']['exe_exercicio']} ({$e['exercicio']['mus_musculo']})" : '<fg=red>NÃO ACHOU NO CATÁLOGO</>';
                $prescricao = implode(' x ', array_filter([$e['tex_serie'], $e['tex_repeticao']]));
                $extras = implode(' · ', array_filter([$e['metodo']['nome'] ?? null, $e['pausa']['nome'] ?? null, $e['tex_obs']]));
                $this->line(sprintf('  %d. [%s] → %s  %s%s', $i + 1, $e['nome_pedido'], $nome, $prescricao, $extras ? "  ({$extras})" : ''));
            }
        }
        $u = $r['uso'];
        $this->newLine();
        $this->line(sprintf('%.1f s · %d tokens de entrada, %d de saída · ~US$ %s', microtime(true) - $inicio, $u['tokens_entrada'], $u['tokens_saida'], number_format($u['custo_usd'], 4, ',', '.')));

        return self::SUCCESS;
    }
}
