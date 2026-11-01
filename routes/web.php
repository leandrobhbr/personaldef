<?php

use App\Http\Controllers\PreviaController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');

// Prévia com senha: mostra os depoimentos de exemplo só para quem tem a senha (SITE_PREVIA_SENHA)
Route::get('/previa', [PreviaController::class, 'form'])->name('previa');
Route::post('/previa', [PreviaController::class, 'entrar'])->middleware('throttle:5,1')->name('previa.entrar');
Route::get('/previa/sair', [PreviaController::class, 'sair'])->name('previa.sair');
