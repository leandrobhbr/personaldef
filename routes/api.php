<?php

use App\Http\Controllers\Aplicativo\AcessoController;
use App\Http\Controllers\Aplicativo\AlunoFichaController;
use App\Http\Controllers\Aplicativo\Personal\AlunoController;
use App\Http\Controllers\Aplicativo\Personal\CatalogoController;
use App\Http\Controllers\Aplicativo\Personal\FichaController;
use App\Http\Controllers\Aplicativo\PublicoController;
use App\Http\Controllers\Aplicativo\PushController;
use Illuminate\Support\Facades\Route;

// Aplicativo de fichas (PWA em /app). Todos os ids são hashids de 6 caracteres.
Route::prefix('app')->group(function () {
    // Área livre: fichas genéricas do Denilson
    Route::get('fichas-publicas', [PublicoController::class, 'fichas']);
    Route::get('fichas-publicas/{treHashid}', [PublicoController::class, 'ficha']);

    // Link mágico / código de 6 dígitos -> token do aparelho
    Route::post('entrar', [AcessoController::class, 'entrar'])->middleware('throttle:10,1');

    // Avisos de nova versão (Web Push): qualquer aparelho, com ou sem link mágico
    Route::get('push/chave', [PushController::class, 'chave']);
    Route::post('push/assinar', [PushController::class, 'assinar'])->middleware('throttle:20,1');
    Route::post('push/cancelar', [PushController::class, 'cancelar']);

    Route::middleware('acesso.app')->group(function () {
        Route::get('eu', [AcessoController::class, 'eu']);
        Route::post('sair', [AcessoController::class, 'sair']);
        Route::get('minhas-fichas', [AlunoFichaController::class, 'index']);
        Route::get('minhas-fichas/{treHashid}', [AlunoFichaController::class, 'show']);
    });

    // Área do personal (Denilson)
    Route::middleware('acesso.app:personal')->prefix('personal')->group(function () {
        Route::get('alunos', [AlunoController::class, 'index']);
        Route::post('alunos', [AlunoController::class, 'store']);
        Route::get('alunos/{aluHashid}', [AlunoController::class, 'show']);
        Route::put('alunos/{aluHashid}', [AlunoController::class, 'update']);
        Route::delete('alunos/{aluHashid}', [AlunoController::class, 'destroy']);
        Route::post('alunos/{aluHashid}/link', [AlunoController::class, 'link']);
        Route::get('alunos/{aluHashid}/aparelhos', [AlunoController::class, 'aparelhos']);
        Route::delete('aparelhos/{aceHashid}', [AlunoController::class, 'revogarAparelho']);

        Route::get('fichas', [FichaController::class, 'index']);
        Route::post('fichas', [FichaController::class, 'store']);
        Route::post('fichas/ordenar', [FichaController::class, 'ordenar']);
        Route::get('fichas/{treHashid}', [FichaController::class, 'show']);
        Route::put('fichas/{treHashid}', [FichaController::class, 'update']);
        Route::delete('fichas/{treHashid}', [FichaController::class, 'destroy']);
        Route::post('fichas/{treHashid}/copiar', [FichaController::class, 'copiar']);
        Route::post('fichas/{treHashid}/exercicios', [FichaController::class, 'adicionarExercicio']);
        Route::post('fichas/{treHashid}/exercicios/ordenar', [FichaController::class, 'ordenarExercicios']);
        Route::put('exercicios-ficha/{texHashid}', [FichaController::class, 'atualizarExercicio']);
        Route::delete('exercicios-ficha/{texHashid}', [FichaController::class, 'removerExercicio']);

        Route::get('catalogo', [CatalogoController::class, 'index']);
        Route::post('catalogo/exercicios', [CatalogoController::class, 'salvarExercicio']);
        Route::put('catalogo/exercicios/{exeHashid}', [CatalogoController::class, 'salvarExercicio']);
    });
});
