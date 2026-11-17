<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Questionário dos serviços: modal na home (uma pergunta por vez), sem banco. A mensagem do WhatsApp é montada no
 * navegador; aqui se confere o que a home entrega, os botões "Solicitar orçamento" e o endereço /questionario.
 */
class QuestionarioTest extends TestCase
{
    public function test_home_tem_o_modal_com_as_10_perguntas_e_o_aviso_lgpd(): void
    {
        $r = $this->get('/')->assertOk()
            ->assertSee('id="quest-modal"', false)
            ->assertSee('Seus dados (LGPD)')
            ->assertSee('sem o seu nome nem o seu telefone')
            ->assertSee(config('site.questionario_lgpd.consentimento'));
        $html = $r->getContent();

        $this->assertSame(10, substr_count($html, 'data-q data-rotulo'));
        $this->assertSame(12, substr_count($html, 'data-passo')); // nome + 10 perguntas + LGPD
        foreach (config('site.questionario') as $q) {
            $r->assertSee($q['pergunta']);
        }
        // a mensagem de cada serviço vai para o modal (1ª linha do WhatsApp)
        $r->assertSee('Quero solicitar um orçamento da Consultoria de Treino Online.')
            ->assertSee('Quero solicitar um orçamento do Personal Trainer Presencial em Marília.');
    }

    public function test_botoes_de_orcamento_abrem_o_questionario_e_sem_js_caem_no_whatsapp(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/href="https:\/\/wa\.me\/[^"]+"[^>]*data-quest-open="online">Solicitar orçamento/', $html);
        $this->assertMatchesRegularExpression('/href="https:\/\/wa\.me\/[^"]+"[^>]*data-quest-open="presencial">Solicitar orçamento/', $html);
        // os outros botões de WhatsApp continuam abrindo o modal do WhatsApp
        $this->assertStringContainsString('data-wa-open', $html);
    }

    public function test_bloco_de_concursos_abre_o_questionario_com_o_objetivo_marcado(): void
    {
        $r = $this->get('/')->assertOk()
            ->assertSee('id="concursos"', false)
            ->assertSee('Preparação para concurso com teste físico')
            ->assertSee('href="'.route('home').'#concursos"', false); // link no menu
        foreach (config('site.concurso.opcoes') as [$op]) {
            $r->assertSee('value="'.$op.'"', false);
        }
        $r->assertSee('data-quest-open="concurso" data-quest-objetivo="Preparação para concurso com teste físico"', false)
            ->assertSee('Quero me preparar para o teste físico de um concurso.');

        // o objetivo marcado pelo bloco existe na pergunta 1 do questionário
        $this->assertContains(config('site.concurso.objetivo'), config('site.questionario.0.opcoes'));
    }

    public function test_endereco_questionario_abre_a_home_com_o_modal(): void
    {
        $this->get('/questionario?tipo=presencial')->assertRedirect('/?questionario=presencial');
        $this->get('/questionario?tipo=online')->assertRedirect('/?questionario=online');
        $this->get('/questionario')->assertRedirect('/?questionario=online');
        $this->get('/questionario?tipo=outro')->assertRedirect('/?questionario=online');
    }
}
