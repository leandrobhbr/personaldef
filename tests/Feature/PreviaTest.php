<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\TestCase;

class PreviaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['site.previa_senha' => 'segredo123']);
        config(['site.depoimentos' => [['exemplo' => true, 'texto' => 'Texto de exemplo.', 'nome' => 'Nome Fictício', 'contexto' => 'Teste']]]);
        // simula produção (onde os exemplos ficam ocultos); o token CSRF do formulário não é o foco aqui
        $this->app['env'] = 'production';
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_em_producao_os_exemplos_ficam_ocultos_sem_a_senha(): void
    {
        $this->get('/')->assertOk()->assertDontSee('Texto de exemplo.');
        $this->get('/previa')->assertOk()->assertSee('name="senha"', false)->assertSee('noindex', false);
    }

    public function test_senha_errada_nao_libera(): void
    {
        $this->from('/previa')->post('/previa', ['senha' => 'errada'])->assertRedirect('/previa')->assertSessionHasErrors('senha');
        $this->get('/')->assertDontSee('Texto de exemplo.');
    }

    public function test_senha_certa_mostra_os_depoimentos_ate_sair(): void
    {
        $this->post('/previa', ['senha' => 'segredo123'])->assertRedirect(route('home').'#depoimentos');
        $this->get('/')->assertSee('Texto de exemplo.')->assertSee('Modo prévia');

        $this->get('/previa/sair')->assertRedirect(route('home'));
        $this->get('/')->assertDontSee('Texto de exemplo.');
    }

    public function test_sem_senha_configurada_a_previa_fica_desligada(): void
    {
        config(['site.previa_senha' => null]);

        $this->post('/previa', ['senha' => ''])->assertSessionHasErrors('senha');
        $this->get('/')->assertDontSee('Texto de exemplo.');
    }
}
