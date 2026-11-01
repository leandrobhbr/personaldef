<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PreviaController extends Controller
{
    /**
     * Formulário de senha da prévia (/previa).
     */
    public function form(): View
    {
        return view('pages.previa', ['pagina' => 'previa']);
    }

    /**
     * Confere a senha (SITE_PREVIA_SENHA) e libera os depoimentos de exemplo nesta sessão.
     */
    public function entrar(Request $request): RedirectResponse
    {
        $senha = (string) config('site.previa_senha');

        if ($senha === '' || ! hash_equals($senha, (string) $request->input('senha'))) {
            return back()->withErrors(['senha' => 'Senha incorreta.']);
        }

        $request->session()->regenerate();
        $request->session()->put('previa', true);

        return redirect(route('home').'#depoimentos');
    }

    /**
     * Encerra a prévia.
     */
    public function sair(Request $request): RedirectResponse
    {
        $request->session()->forget('previa');

        return redirect()->route('home');
    }
}
