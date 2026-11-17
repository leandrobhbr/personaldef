<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Questionário dos serviços (/questionario?tipo=online|presencial): página sem banco, que monta a mensagem do WhatsApp
 * no navegador. Aqui se confere o que a página entrega e os botões "Solicitar orçamento" da home.
 */
class QuestionarioTest extends TestCase
{
    public function test_questionario_tem_as_10_perguntas_o_aviso_lgpd_e_nao_e_indexado(): void
    {
        $r = $this->get('/questionario?tipo=online')->assertOk()
            ->assertSee('noindex,nofollow', false)
            ->assertSee('Consultoria de Treino Online')
            ->assertSee('data-intro="Olá Denilson! Quero solicitar um orçamento da Consultoria de Treino Online."', false)
            ->assertSee('Seus dados (LGPD)')
            ->assertSee('sem o seu nome nem o seu telefone')
            ->assertSee(config('site.questionario_lgpd.consentimento'));

        $this->assertSame(10, substr_count($r->getContent(), 'data-q '));
        foreach (config('site.questionario') as $q) {
            $r->assertSee($q['pergunta']);
        }
    }

    public function test_presencial_usa_o_mesmo_questionario_com_a_mensagem_dele(): void
    {
        $this->get('/questionario?tipo=presencial')->assertOk()
            ->assertSee('<span class="tag">Personal Trainer Presencial</span>', false)
            ->assertSee('data-intro="Olá Denilson! Quero solicitar um orçamento do Personal Trainer Presencial em Marília."', false);

        // tipo desconhecido ou ausente: consultoria online
        $this->get('/questionario')->assertOk()->assertSee('<span class="tag">Consultoria de Treino Online</span>', false);
        $this->get('/questionario?tipo=outro')->assertOk()->assertSee('<span class="tag">Consultoria de Treino Online</span>', false);
    }

    public function test_botoes_de_orcamento_da_home_levam_ao_questionario(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('href="'.url('/questionario?tipo=online').'">Solicitar orçamento</a>', $html);
        $this->assertStringContainsString('href="'.url('/questionario?tipo=presencial').'">Solicitar orçamento</a>', $html);
        // os outros botões de WhatsApp continuam abrindo o modal
        $this->assertStringContainsString('data-wa-open', $html);
    }
}
