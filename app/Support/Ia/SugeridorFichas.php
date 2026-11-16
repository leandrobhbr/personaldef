<?php

namespace App\Support\Ia;

/**
 * Lê o pedido do personal (texto livre) e devolve as fichas montadas com exercícios do catálogo (ver
 * ClaudeSugeridorFichas::esquema). Interface para os testes trocarem a IA por respostas fixas.
 */
interface SugeridorFichas
{
    /**
     * @param  string  $catalogo  exercícios, métodos, cadências e descansos, um por linha, com o id na frente
     * @return array{dados: array<string, mixed>, uso: array{tokens_entrada: int, tokens_saida: int}}
     */
    public function sugerir(string $catalogo, string $pedido): array;
}
