<?php

namespace Tests\Feature;

use Tests\TestCase;

class DepoimentosTest extends TestCase
{
    public function test_depoimentos_com_botao_de_fotos(): void
    {
        config(['site.depoimentos' => [['texto' => 'Treino excelente.', 'nome' => 'Ana Souza', 'contexto' => 'Consultoria online', 'fotos' => ['images/academia.webp']]]]);

        $this->get('/')->assertOk()
            ->assertSee('id="depoimentos"', false)
            ->assertSee('Treino excelente.')
            ->assertSee('>AS<', false)
            ->assertSee('data-galeria=', false);
    }

    public function test_depoimentos_de_exemplo_nunca_aparecem_em_producao(): void
    {
        config(['site.depoimentos' => [['exemplo' => true, 'texto' => 'Texto de exemplo.', 'nome' => 'Nome Fictício', 'contexto' => 'Teste']]]);
        $this->get('/')->assertSee('Texto de exemplo.');

        $this->app['env'] = 'production';
        $this->get('/')->assertDontSee('Texto de exemplo.')->assertDontSee('id="depoimentos"', false);
    }
}
