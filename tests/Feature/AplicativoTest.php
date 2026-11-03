<?php

namespace Tests\Feature;

use App\Models\Aluno;
use App\Models\Exercicio;
use App\Models\Musculo;
use App\Models\Treino;
use App\Support\LinkMagico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AplicativoTest extends TestCase
{
    use RefreshDatabase;

    private Exercicio $exercicio;

    protected function setUp(): void
    {
        parent::setUp();
        $musculo = Musculo::create(['mus_musculo' => 'Peito']);
        $this->exercicio = Exercicio::create(['exe_exercicio' => 'Supino reto', 'mus_id' => $musculo->mus_id, 'exe_gif' => 'supino.gif']);
    }

    private function ficha(array $dados = []): Treino
    {
        $treino = Treino::create($dados + ['tre_titulo' => 'Treino A', 'tre_data_cadastro' => '2026-11-03 14:30:00']);
        $treino->exercicios()->create(['exe_id' => $this->exercicio->exe_id, 'mus_id' => $this->exercicio->mus_id, 'tex_serie' => '4', 'tex_repeticao' => '10']);

        return $treino;
    }

    private function aluno(string $nome = 'Camila Rocha'): Aluno
    {
        $aluno = Aluno::create(['alu_nome' => $nome]);
        $aluno->telefones()->create(['tel_numero' => '5514999990000', 'tel_principal' => true]);

        return $aluno;
    }

    /** Libera um aparelho pelo link mágico e devolve o token dele. */
    private function tokenDe(?Aluno $aluno, string $perfil = 'ALU'): string
    {
        $link = LinkMagico::gerar($aluno, $perfil)['link'];

        return $this->postJson('/api/app/entrar', ['token' => substr($link, strrpos($link, '/') + 1)])->assertOk()->json('token');
    }

    public function test_hashids_tem_6_caracteres_e_nenhum_id_numerico_sai_na_api(): void
    {
        $treino = $this->ficha(['tre_publico' => true]);
        $this->assertSame(6, strlen($treino->tre_hashid));

        $json = $this->getJson('/api/app/fichas-publicas/'.$treino->tre_hashid)->assertOk()->getContent();
        $this->assertDoesNotMatchRegularExpression('/"(tre|tex|exe|mus|alu|ace)_id"/', $json);
        $this->assertStringContainsString('"tre_hashid":"'.$treino->tre_hashid.'"', $json);
    }

    public function test_area_livre_mostra_so_fichas_publicas_com_o_aviso(): void
    {
        $publica = $this->ficha(['tre_publico' => true, 'tre_titulo' => 'Iniciante']);
        $privada = $this->ficha(['tre_titulo' => 'Rascunho']);
        $doAluno = $this->ficha(['alu_id' => $this->aluno()->alu_id, 'tre_titulo' => 'Da Camila']);

        $this->getJson('/api/app/fichas-publicas')->assertOk()
            ->assertJsonPath('aviso', 'Deseja uma ficha personalizada? Acesse personaldef.com.br')
            ->assertJsonCount(1, 'fichas')
            ->assertJsonPath('fichas.0.tre_titulo', 'Iniciante');

        $this->getJson('/api/app/fichas-publicas/'.$privada->tre_hashid)->assertNotFound();
        $this->getJson('/api/app/fichas-publicas/'.$doAluno->tre_hashid)->assertNotFound();
        $this->getJson('/api/app/fichas-publicas/xxxxxx')->assertNotFound();
        $this->getJson('/api/app/fichas-publicas/'.$publica->tre_hashid)->assertOk()->assertJsonPath('exercicios.0.exercicio.exe_exercicio', 'Supino reto');
    }

    public function test_link_magico_vale_uma_vez_e_o_codigo_tambem(): void
    {
        $aluno = $this->aluno();
        ['link' => $link, 'codigo' => $codigo] = LinkMagico::gerar($aluno);
        $token = substr($link, strrpos($link, '/') + 1);

        $this->postJson('/api/app/entrar', ['token' => $token])->assertOk()->assertJsonPath('perfil', 'aluno')->assertJsonPath('aluno.alu_nome', 'Camila Rocha');
        $this->postJson('/api/app/entrar', ['token' => $token])->assertStatus(422);
        $this->postJson('/api/app/entrar', ['token' => $codigo])->assertStatus(422);

        // código de 6 dígitos (app já instalado) libera outro link
        $codigo2 = LinkMagico::gerar($aluno)['codigo'];
        $this->postJson('/api/app/entrar', ['token' => $codigo2])->assertOk();

        // vencido
        $velho = LinkMagico::gerar($aluno);
        $velho['acesso']->forceFill(['ace_link_expira' => now()->subMinute()])->save();
        $this->postJson('/api/app/entrar', ['token' => $velho['codigo']])->assertStatus(422);
    }

    public function test_aluno_ve_so_as_proprias_fichas_com_nome_e_data_no_topo(): void
    {
        $camila = $this->aluno();
        $rafael = $this->aluno('Rafael Mendes');
        $daCamila = $this->ficha(['alu_id' => $camila->alu_id, 'tre_titulo' => 'Treino da Camila']);
        $doRafael = $this->ficha(['alu_id' => $rafael->alu_id, 'tre_titulo' => 'Treino do Rafael']);

        $token = $this->tokenDe($camila);
        $auth = ['Authorization' => 'Bearer '.$token];

        $this->getJson('/api/app/minhas-fichas', $auth)->assertOk()->assertJsonCount(1, 'fichas')->assertJsonPath('fichas.0.tre_titulo', 'Treino da Camila');
        $this->getJson('/api/app/minhas-fichas/'.$daCamila->tre_hashid, $auth)->assertOk()
            ->assertJsonPath('aluno.alu_nome', 'Camila Rocha')
            ->assertJsonPath('tre_data_cadastro', '03/11/2026 14:30');
        $this->getJson('/api/app/minhas-fichas/'.$doRafael->tre_hashid, $auth)->assertNotFound();
        $this->getJson('/api/app/minhas-fichas')->assertUnauthorized();
    }

    public function test_aparelho_desconectado_perde_o_acesso(): void
    {
        $aluno = $this->aluno();
        $auth = ['Authorization' => 'Bearer '.$this->tokenDe($aluno)];
        $this->getJson('/api/app/eu', $auth)->assertOk();

        $this->postJson('/api/app/sair', [], $auth)->assertOk();
        $this->getJson('/api/app/eu', $auth)->assertUnauthorized();

        // aluno inativado também perde
        $auth2 = ['Authorization' => 'Bearer '.$this->tokenDe($aluno)];
        $aluno->update(['alu_situacao' => 'IN']);
        $this->getJson('/api/app/minhas-fichas', $auth2)->assertUnauthorized();
    }

    public function test_personal_entra_com_senha_fixa(): void
    {
        config(['aplicativo.senha_personal' => 'senha-teste']);

        $this->postJson('/api/app/entrar-personal', ['senha' => 'errada'])->assertStatus(422);
        $token = $this->postJson('/api/app/entrar-personal', ['senha' => 'senha-teste'])
            ->assertOk()->assertJsonPath('perfil', 'personal')->json('token');
        $this->getJson('/api/app/personal/alunos', ['Authorization' => "Bearer {$token}"])->assertOk();
    }

    public function test_area_do_personal_e_exclusiva_do_denilson(): void
    {
        $aluno = ['Authorization' => 'Bearer '.$this->tokenDe($this->aluno())];
        $this->getJson('/api/app/personal/alunos', $aluno)->assertForbidden();
        $this->getJson('/api/app/personal/alunos')->assertUnauthorized();
    }

    public function test_personal_cadastra_aluno_gera_link_e_copia_ficha(): void
    {
        $auth = ['Authorization' => 'Bearer '.$this->tokenDe(null, 'PER')];

        $novo = $this->postJson('/api/app/personal/alunos', ['alu_nome' => 'Sônia Prado', 'telefones' => ['(14) 99629-5320', '14 3433-1234']], $auth)
            ->assertCreated()->assertJsonPath('telefones', ['5514996295320', '551434331234'])->json();
        $this->assertSame(6, strlen($novo['alu_hashid']));

        $link = $this->postJson('/api/app/personal/alunos/'.$novo['alu_hashid'].'/link', [], $auth)->assertOk()->json();
        $this->assertCount(2, $link['whatsapp']);
        $this->assertStringStartsWith('https://wa.me/5514996295320?text=', $link['whatsapp'][0]['url']);
        $this->assertMatchesRegularExpression('/^\d{6}$/', $link['codigo']);

        $generica = $this->ficha(['tre_publico' => true]);
        $copia = $this->postJson('/api/app/personal/fichas/'.$generica->tre_hashid.'/copiar', ['alu_hashid' => $novo['alu_hashid']], $auth)
            ->assertCreated()->assertJsonPath('aluno.alu_nome', 'Sônia Prado')->assertJsonPath('tre_publico', false)->json();
        $this->assertCount(1, $copia['exercicios']);

        // monta ficha: adiciona exercício por hashid
        $this->postJson('/api/app/personal/fichas/'.$copia['tre_hashid'].'/exercicios', ['exe_hashid' => $this->exercicio->exe_hashid, 'tex_serie' => '3'], $auth)
            ->assertCreated()->assertJsonPath('exercicio.exe_hashid', $this->exercicio->exe_hashid);
        $this->getJson('/api/app/personal/catalogo', $auth)->assertOk()->assertJsonPath('musculos.0.exercicios.0.exe_exercicio', 'Supino reto');
    }
}
