@extends('layouts.site')

@section('title', 'Prévia')
@section('robots', 'noindex,nofollow')

@section('content')

<section class="wrap sec" style="display: flex; flex-direction: column; align-items: center; gap: 24px; text-align: center">
  <span class="tag" style="align-self: center">Acesso restrito</span>
  <h1 class="h2">Prévia do site</h1>
  <p class="lead" style="max-width: 460px">Digite a senha para ver os depoimentos ilustrativos da página inicial.</p>
  @if (config('site.previa_senha'))
    <form class="previa-form" method="post" action="{{ route('previa.entrar') }}" style="text-align: left">
      @csrf
      <label for="senha">Senha</label>
      <input id="senha" name="senha" type="password" autocomplete="current-password" required autofocus>
      @error('senha')
        <p class="previa-erro" role="alert">{{ $message }}</p>
      @enderror
      <button type="submit" class="btn btn-d btn-lg" style="border: 0">Ver prévia</button>
    </form>
  @else
    <p class="previa-erro">A prévia está desligada (defina SITE_PREVIA_SENHA no .env).</p>
  @endif
</section>

@endsection
