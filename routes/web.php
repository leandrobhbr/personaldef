<?php

use App\Http\Controllers\PreviaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

// Questionário dos serviços: é um modal na home; este endereço abre a home com ele aberto (?tipo=online|presencial)
Route::get('/questionario', fn (Request $request) => redirect('/?questionario='.($request->query('tipo') === 'presencial' ? 'presencial' : 'online')))->name('questionario');

// Prévia com senha: mostra os depoimentos de exemplo só para quem tem a senha (SITE_PREVIA_SENHA)
Route::get('/previa', [PreviaController::class, 'form'])->name('previa');
Route::post('/previa', [PreviaController::class, 'entrar'])->middleware('throttle:5,1')->name('previa.entrar');
Route::get('/previa/sair', [PreviaController::class, 'sair'])->name('previa.sair');

// Atalho do Denilson: /app/personal abre a área do personal (o app usa rotas com #)
Route::redirect('/app/personal', '/app/#/personal');
