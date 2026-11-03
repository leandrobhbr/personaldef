<?php

namespace App\Console\Commands;

use App\Http\Controllers\Aplicativo\Personal\AlunoController;
use App\Models\Aluno;
use App\Models\Cadencia;
use App\Models\Exercicio;
use App\Models\Metodo;
use App\Models\Musculo;
use App\Models\Pausa;
use App\Models\Treino;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Importa do banco do 69fit só o necessário para o app de fichas: catálogo (músculos, exercícios,
 * métodos, cadências, pausas), as fichas do Denilson (viram genéricas) e os alunos dele com as fichas
 * e o telefone. Lê a conexão "mysql_69fit" (DB_69FIT_* no .env). Pode rodar de novo: não duplica.
 */
class ImportarFichas69fit extends Command
{
    protected $signature = 'app:importar-69fit
        {email : e-mail do Denilson no 69fit}
        {--midias= : pasta storage/app/uploads/exercicios do 69fit, para copiar imagens e GIFs}
        {--sem-alunos : importa só catálogo e fichas genéricas}';

    protected $description = 'Importa catálogo, fichas e alunos do 69fit para o aplicativo de fichas';

    public function handle(): int
    {
        $antigo = DB::connection('mysql_69fit');
        $personal = $antigo->table('users')->where('email', $this->argument('email'))->first();
        if (! $personal) {
            $this->error('Usuário não encontrado no 69fit.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($antigo, $personal) {
            // catálogo: mapa id antigo -> id novo
            $mapa = [];
            foreach (['tb_musculo' => [Musculo::class, 'mus', 'mus_musculo'], 'tb_metodo' => [Metodo::class, 'met', 'met_metodo'], 'tb_cadencia' => [Cadencia::class, 'cad', 'cad_cadencia'], 'tb_pausa' => [Pausa::class, 'pau', 'pau_pausa']] as $tabela => [$classe, $p, $nome]) {
                foreach ($antigo->table($tabela)->get() as $linha) {
                    $novo = $classe::firstOrCreate([$nome => $linha->{$nome}], [
                        "{$p}_descricao" => $linha->{"{$p}_descricao"} ?? null,
                        "{$p}_situacao" => $linha->{"{$p}_situacao"} ?? 'AT',
                    ]);
                    $mapa[$p][$linha->{"{$p}_id"}] = $novo->getKey();
                }
                $this->line("{$tabela}: ".count($mapa[$p] ?? []));
            }

            foreach ($antigo->table('tb_exercicio')->get() as $e) {
                if (! isset($mapa['mus'][$e->mus_id])) {
                    continue;
                }
                $novo = Exercicio::firstOrCreate(['exe_exercicio' => $e->exe_exercicio, 'mus_id' => $mapa['mus'][$e->mus_id]], [
                    'exe_situacao' => $e->exe_situacao, 'exe_video' => $e->exe_video, 'exe_imagem' => $e->exe_imagem,
                    'exe_gif' => $e->exe_gif, 'exe_audio' => $e->exe_audio, 'exe_explicacao' => $e->exe_explicacao,
                ]);
                $mapa['exe'][$e->exe_id] = $novo->exe_id;
            }
            $this->line('tb_exercicio: '.count($mapa['exe'] ?? []));

            // fichas do Denilson viram genéricas (privadas: ele escolhe quais deixar públicas no app)
            $qtd = $this->importarTreinos($antigo, $personal->id, null, $mapa);
            $this->line("fichas genéricas: {$qtd}");

            if (! $this->option('sem-alunos')) {
                $alunos = $antigo->table('users')->where('usu_id_personal', $personal->id)->where('usu_situacao', 'AT')->get();
                foreach ($alunos as $u) {
                    $aluno = Aluno::firstOrCreate(['alu_nome' => $u->name]);
                    if ($tel = AlunoController::normalizarTelefone((string) $u->usu_telefone)) {
                        $aluno->telefones()->firstOrCreate(['tel_numero' => $tel], ['tel_principal' => true]);
                    }
                    $this->importarTreinos($antigo, $u->id, $aluno->alu_id, $mapa);
                }
                $this->line('alunos: '.$alunos->count());
            }
        });

        if ($pasta = $this->option('midias')) {
            $destino = storage_path('app/public/exercicios');
            File::ensureDirectoryExists($destino);
            $copiados = 0;
            foreach (Exercicio::all(['exe_imagem', 'exe_gif']) as $e) {
                foreach ([$e->exe_imagem, $e->exe_gif] as $arquivo) {
                    if ($arquivo && ! str_starts_with($arquivo, 'http') && is_file("{$pasta}/{$arquivo}") && ! is_file("{$destino}/{$arquivo}")) {
                        File::copy("{$pasta}/{$arquivo}", "{$destino}/{$arquivo}");
                        $copiados++;
                    }
                }
            }
            $this->line("mídias copiadas: {$copiados}");
        }

        $this->info('Importação concluída.');

        return self::SUCCESS;
    }

    private function importarTreinos($antigo, int $usuId, ?int $alunoId, array $mapa): int
    {
        $qtd = 0;
        foreach ($antigo->table('tb_treino')->where('usu_id', $usuId)->where('tre_situacao', '<>', 'IN')->get() as $t) {
            $treino = Treino::firstOrCreate(['tre_titulo' => $t->tre_titulo, 'alu_id' => $alunoId], [
                'tre_obs_professor' => $t->tre_obs_professor, 'tre_data_cadastro' => $t->tre_data_cadastro,
                'tre_ordem' => $t->tre_ordem, 'tre_situacao' => 'AT', 'tre_publico' => false,
            ]);
            if (! $treino->wasRecentlyCreated) {
                continue;
            }
            foreach ($antigo->table('tb_treino_exercicio')->where('tre_id', $t->tre_id)->where('tex_situacao', 'AT')->orderBy('tex_ordem')->get() as $x) {
                if (! isset($mapa['exe'][$x->exe_id])) {
                    continue;
                }
                $treino->exercicios()->create([
                    'exe_id' => $mapa['exe'][$x->exe_id],
                    'mus_id' => $mapa['mus'][$x->mus_id] ?? null,
                    'pau_id' => $mapa['pau'][$x->pau_id] ?? null,
                    'cad_id' => $mapa['cad'][$x->cad_id] ?? null,
                    'met_id' => $mapa['met'][$x->met_id] ?? null,
                ] + (array) collect($x)->only(['tex_ordem', 'tex_serie', 'tex_repeticao', 'tex_carga', 'tex_tempo', 'tex_obs', 'tex_grupo_cor'])->all());
            }
            $qtd++;
        }

        return $qtd;
    }
}
