<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Exercicio;
use App\Models\Metodo;
use App\Models\Musculo;
use App\Models\Pausa;
use App\Models\Treino;
use App\Services\FichaIaService;
use App\Support\Ia\ClaudeSugeridorFichas;
use App\Support\Ia\SugeridorFichas;
use App\Support\LinkMagico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * "Fichas com IA" e PDF das fichas. A IA é trocada por um sugeridor falso que devolve o que o Claude devolveria para
 * um pedido como o do Denilson ("TREINO A COSTAS LEVANTAMENTO TERRA 3X10-15 ... FICHA B PEITORAL ...").
 */
class FichaIaTest extends TestCase
{
    use RefreshDatabase;

    private array $exe = [];

    private array $headers;

    private ?string $catalogoRecebido = null;

    protected function setUp(): void
    {
        parent::setUp();
        $costas = Musculo::create(['mus_musculo' => 'Costas']);
        $peito = Musculo::create(['mus_musculo' => 'Peito']);
        // GIFs reais do catálogo, quando a pasta existir (para o PDF sair com os quadros)
        $this->exe['terra'] = Exercicio::create(['exe_exercicio' => 'Levantamento terra', 'mus_id' => $costas->mus_id, 'exe_gif' => '1152_pernas_leg-press.gif']);
        $this->exe['puxada'] = Exercicio::create(['exe_exercicio' => 'Barra fixa', 'mus_id' => $costas->mus_id, 'exe_gif' => '599_costas_barra-fixa.gif']);
        $this->exe['supino'] = Exercicio::create(['exe_exercicio' => 'Supino reto (barra)', 'mus_id' => $peito->mus_id, 'exe_gif' => '267_peito_supino-reto-barra.gif']);
        $this->exe['inativo'] = Exercicio::create(['exe_exercicio' => 'Antigo', 'mus_id' => $peito->mus_id, 'exe_situacao' => 'IN']);
        Metodo::create(['met_metodo' => '-']);
        Metodo::create(['met_metodo' => 'Conjugado', 'met_descricao' => 'Sem descanso entre os dois']);
        Pausa::create(['pau_pausa' => '60"']);

        config(['aplicativo.senha_personal' => 'senha-teste']);
        $token = $this->postJson('/api/app/entrar-personal', ['senha' => 'senha-teste'])->json('token');
        $this->headers = ['Authorization' => "Bearer {$token}"];
    }

    /** a IA "devolve" sempre $dados */
    private function iaDevolve(array $dados): void
    {
        $teste = $this;
        $this->app->instance(SugeridorFichas::class, new class($dados, $teste) implements SugeridorFichas
        {
            public function __construct(private array $dados, private FichaIaTest $teste) {}

            public function sugerir(string $catalogo, string $pedido): array
            {
                $this->teste->guardarCatalogo($catalogo);

                return ['dados' => $this->dados, 'uso' => ['tokens_entrada' => 9000, 'tokens_saida' => 1500]];
            }
        });
    }

    public function guardarCatalogo(string $catalogo): void
    {
        $this->catalogoRecebido = $catalogo;
    }

    private function item(string $nome, ?int $id, array $extra = []): array
    {
        return $extra + ['nome_pedido' => $nome, 'exercicio_id' => $id, 'alternativas' => [], 'series' => '3', 'repeticoes' => '10-15',
            'carga' => '', 'tempo' => '', 'descanso_id' => null, 'metodo_id' => null, 'cadencia_id' => null, 'observacao' => ''];
    }

    public function test_sugere_varias_fichas_casando_o_catalogo_sem_gravar_nada(): void
    {
        $conjugado = Metodo::where('met_metodo', 'Conjugado')->first();
        $this->iaDevolve([
            'resumo' => 'Duas fichas: A costas, B peito.',
            'avisos' => ['"Remada cabo chão" sem nº de séries — usei a regra geral 3x10-15.'],
            'fichas' => [
                ['titulo' => 'Treino A · Costas', 'orientacoes' => 'Repetições de 10 a 15 em todos.', 'exercicios' => [
                    $this->item('Levantamento terra', $this->exe['terra']->exe_id, ['alternativas' => [$this->exe['puxada']->exe_id, 999]]),
                    $this->item('Puxada na barra', $this->exe['puxada']->exe_id, ['metodo_id' => $conjugado->met_id, 'repeticoes' => '10 a 15 repetições']),
                    $this->item('Trapézio com halteres', null),
                ]],
                ['titulo' => 'Treino B · Peito', 'orientacoes' => '', 'exercicios' => [
                    $this->item('Supino reto', $this->exe['supino']->exe_id),
                    $this->item('Exercício inativo', $this->exe['inativo']->exe_id),
                ]],
            ],
        ]);

        $r = $this->postJson('/api/app/personal/ia/sugerir', ['pedido' => 'TREINO A COSTAS LEVANTAMENTO TERRA 3X10-15 ... FICHA B PEITORAL'], $this->headers)
            ->assertOk()
            ->assertJsonCount(2, 'fichas')
            ->assertJsonPath('fichas.0.titulo', 'Treino A · Costas')
            ->assertJsonPath('fichas.0.exercicios.0.exercicio.exe_exercicio', 'Levantamento terra')
            ->assertJsonPath('fichas.0.exercicios.0.tex_serie', '3')
            ->assertJsonPath('fichas.0.exercicios.0.tex_repeticao', '10-15')
            // alternativa inexistente (999) some; a válida vem com mídia
            ->assertJsonCount(1, 'fichas.0.exercicios.0.alternativas')
            ->assertJsonPath('fichas.0.exercicios.1.metodo.nome', 'Conjugado')
            // texto longo demais para a coluna (10): cortado no campo e inteiro na observação
            ->assertJsonPath('fichas.0.exercicios.1.tex_repeticao', '10 a 15 re')
            ->assertJsonPath('fichas.0.exercicios.1.tex_obs', 'Repeticoes: 10 a 15 repetições.')
            // não achou no catálogo / exercício inativo: a tela pede para escolher
            ->assertJsonPath('fichas.0.exercicios.2.exercicio', null)
            ->assertJsonPath('fichas.0.exercicios.2.nome_pedido', 'Trapézio com halteres')
            ->assertJsonPath('fichas.1.exercicios.1.exercicio', null)
            ->assertJsonPath('fichas.1.orientacoes', null)
            ->assertJsonPath('uso.custo_usd', 0.0017);

        $this->assertDoesNotMatchRegularExpression('/"(exe|met|pau|cad|mus)_id"/', $r->getContent());
        $this->assertSame(0, Treino::count());

        // catálogo enviado: só ativos, com id na frente, sem a opção "-"
        $this->assertStringContainsString("{$this->exe['terra']->exe_id} Levantamento terra", $this->catalogoRecebido);
        $this->assertStringNotContainsString('Antigo', $this->catalogoRecebido);
        $this->assertStringContainsString("{$conjugado->met_id} Conjugado", $this->catalogoRecebido);
        $this->assertDoesNotMatchRegularExpression('/^\d+ -$/m', $this->catalogoRecebido);
    }

    public function test_confirma_uma_ficha_para_o_aluno_com_os_exercicios_na_ordem(): void
    {
        $aluno = Aluno::create(['alu_nome' => 'Camila Rocha']);
        Treino::create(['tre_titulo' => 'Antiga', 'alu_id' => $aluno->alu_id, 'tre_data_cadastro' => now(), 'tre_ordem' => 1]);
        $metodo = Metodo::where('met_metodo', 'Conjugado')->first();

        $r = $this->postJson('/api/app/personal/ia/confirmar', [
            'alu_hashid' => $aluno->alu_hashid,
            'tre_titulo' => 'Treino A · Costas',
            'tre_obs_professor' => 'Repetições de 10 a 15.',
            'exercicios' => [
                ['exe_hashid' => $this->exe['puxada']->exe_hashid, 'tex_serie' => '3', 'tex_repeticao' => '10-15', 'met_hashid' => $metodo->met_hashid],
                ['exe_hashid' => $this->exe['terra']->exe_hashid, 'tex_serie' => '4', 'tex_repeticao' => '12', 'tex_obs' => 'Pegada pronada'],
            ],
        ], $this->headers)->assertCreated()
            ->assertJsonPath('tre_titulo', 'Treino A · Costas')
            ->assertJsonPath('aluno.alu_nome', 'Camila Rocha')
            ->assertJsonPath('exercicios.0.exercicio.exe_exercicio', 'Barra fixa')
            ->assertJsonPath('exercicios.0.metodo.nome', 'Conjugado')
            ->assertJsonPath('exercicios.1.tex_obs', 'Pegada pronada');

        $nova = Treino::hashid($r->json('tre_hashid'))->first();
        $this->assertSame(2, $nova->tre_ordem);
        $this->assertSame(2, $nova->exercicios()->count());
        $this->assertSame($this->exe['terra']->mus_id, $nova->exercicios()->where('tex_ordem', 2)->first()->mus_id);
    }

    public function test_confirmar_exige_exercicio_escolhido_e_nao_grava_pela_metade(): void
    {
        $this->postJson('/api/app/personal/ia/confirmar', [
            'tre_titulo' => 'Treino A',
            'exercicios' => [['exe_hashid' => $this->exe['terra']->exe_hashid], ['exe_hashid' => null]],
        ], $this->headers)->assertStatus(422)->assertJsonValidationErrors('exercicios.1.exe_hashid');

        $this->postJson('/api/app/personal/ia/confirmar', [
            'tre_titulo' => 'Treino A',
            'exercicios' => [['exe_hashid' => $this->exe['terra']->exe_hashid], ['exe_hashid' => 'zzzzzz']],
        ], $this->headers)->assertNotFound();

        $this->assertSame(0, Treino::count());
    }

    public function test_so_o_personal_usa_a_ia_e_o_pdf(): void
    {
        $aluno = Aluno::create(['alu_nome' => 'Camila Rocha']);
        $link = LinkMagico::gerar($aluno)['link'];
        $token = $this->postJson('/api/app/entrar', ['token' => substr($link, strrpos($link, '/') + 1)])->json('token');

        $this->postJson('/api/app/personal/ia/sugerir', ['pedido' => 'Treino A costas...'], ['Authorization' => "Bearer {$token}"])->assertForbidden();
        $this->getJson("/api/app/personal/alunos/{$aluno->alu_hashid}/pdf", ['Authorization' => "Bearer {$token}"])->assertForbidden();
    }

    public function test_aluno_baixa_o_pdf_so_das_proprias_fichas(): void
    {
        $camila = Aluno::create(['alu_nome' => 'Camila Rocha']);
        $outro = Aluno::create(['alu_nome' => 'Outro Aluno']);
        $daCamila = Treino::create(['tre_titulo' => 'Treino A', 'alu_id' => $camila->alu_id, 'tre_data_cadastro' => now()]);
        $daCamila->exercicios()->create(['exe_id' => $this->exe['terra']->exe_id, 'mus_id' => $this->exe['terra']->mus_id, 'tex_serie' => '3', 'tex_repeticao' => '12']);
        $doOutro = Treino::create(['tre_titulo' => 'Treino X', 'alu_id' => $outro->alu_id, 'tre_data_cadastro' => now()]);
        $link = LinkMagico::gerar($camila)['link'];
        $token = ['Authorization' => 'Bearer '.$this->postJson('/api/app/entrar', ['token' => substr($link, strrpos($link, '/') + 1)])->json('token')];

        $this->get("/api/app/minhas-fichas/{$daCamila->tre_hashid}/pdf?modelo=simplificado", $token)->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename="Ficha simplificada - Camila Rocha - Treino A.pdf"');
        $this->get("/api/app/minhas-fichas/{$daCamila->tre_hashid}/pdf", $token)->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename="Ficha - Camila Rocha - Treino A.pdf"');
        $this->getJson("/api/app/minhas-fichas/{$doOutro->tre_hashid}/pdf", $token)->assertNotFound();
        $this->getJson("/api/app/minhas-fichas/{$daCamila->tre_hashid}/pdf")->assertUnauthorized();
    }

    public function test_sem_chave_da_api_avisa_em_vez_de_quebrar(): void
    {
        config(['services.anthropic.key' => '']);

        $this->postJson('/api/app/personal/ia/sugerir', ['pedido' => 'Treino A costas levantamento terra'], $this->headers)
            ->assertStatus(422)->assertJsonPath('message', 'A IA não está configurada neste servidor (falta ANTHROPIC_API_KEY no .env).');
    }

    public function test_esquema_da_ia_respeita_os_limites_da_saida_estruturada(): void
    {
        $json = json_encode(ClaudeSugeridorFichas::esquema());
        $this->assertLessThanOrEqual(16, substr_count($json, '"anyOf"'));
        $this->assertSame(1, substr_count(app(FichaIaService::class)->catalogo() ?: 'x', 'Exercícios — Costas'));
    }

    public function test_pdf_de_uma_ficha_e_de_todas_as_do_aluno(): void
    {
        $aluno = Aluno::create(['alu_nome' => 'Camila Rocha']);
        $pausa = Pausa::first();
        foreach (['Treino A · Costas' => ['terra', 'puxada'], 'Treino B · Peito' => ['supino']] as $titulo => $lista) {
            $t = Treino::create(['tre_titulo' => $titulo, 'alu_id' => $aluno->alu_id, 'tre_data_cadastro' => now(), 'tre_obs_professor' => 'Repetições de 10 a 15 em todos.']);
            foreach ($lista as $i => $k) {
                $t->exercicios()->create(['exe_id' => $this->exe[$k]->exe_id, 'mus_id' => $this->exe[$k]->mus_id, 'tex_ordem' => $i + 1,
                    'tex_serie' => '3', 'tex_repeticao' => '10-15', 'tex_carga' => '20 kg', 'pau_id' => $pausa->pau_id, 'tex_obs' => $i ? 'Pegada supinada, controle a descida.' : null]);
            }
        }

        $r = $this->get("/api/app/personal/alunos/{$aluno->alu_hashid}/pdf", $this->headers)->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename="Ficha - Camila Rocha.pdf"');
        $this->assertStringStartsWith('%PDF', $r->getContent());
        if ($saida = env('PDF_TESTE_SAIDA')) {
            file_put_contents($saida, $r->getContent());
        }

        $simples = $this->get("/api/app/personal/alunos/{$aluno->alu_hashid}/pdf?modelo=simplificado", $this->headers)->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename="Ficha simplificada - Camila Rocha.pdf"');
        $this->assertStringStartsWith('%PDF', $simples->getContent());
        $this->assertLessThan(strlen($r->getContent()), strlen($simples->getContent())); // sem imagens
        if ($saida) {
            file_put_contents(str_replace('.pdf', '-simplificado.pdf', $saida), $simples->getContent());
        }
        $this->getJson("/api/app/personal/alunos/{$aluno->alu_hashid}/pdf?modelo=outro", $this->headers)->assertStatus(422);

        $umaFicha = Treino::where('tre_titulo', 'Treino B · Peito')->first();
        $this->get("/api/app/personal/fichas/{$umaFicha->tre_hashid}/pdf", $this->headers)->assertOk()
            ->assertHeader('Content-Disposition', 'inline; filename="Ficha - Camila Rocha - Treino B Peito.pdf"');

        $semFichas = Aluno::create(['alu_nome' => 'Sem fichas']);
        $this->get("/api/app/personal/alunos/{$semFichas->alu_hashid}/pdf", $this->headers)->assertNotFound();
    }
}
