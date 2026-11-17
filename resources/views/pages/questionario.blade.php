@extends('layouts.site')

@php
  // ?tipo=online|presencial escolhe o serviço (título e 1ª linha da mensagem); sem tipo, consultoria online
  $servico = collect(config('site.servicos'))->firstWhere('tipo', request('tipo')) ?? collect(config('site.servicos'))->firstWhere('tipo', 'online');
  $lgpd = config('site.questionario_lgpd');
@endphp

@section('title', 'Questionário · '.$servico['title'])
@section('description', 'Responda 10 perguntas rápidas e envie pelo WhatsApp para receber o orçamento de '.$servico['title'].' do Personal DEF.')
@section('robots', 'noindex,nofollow')

@section('content')

{{-- Questionário dos serviços: nada é salvo no site. O site.js monta a mensagem com as respostas e abre o WhatsApp. --}}
<section class="wrap sec quest" id="questionario" data-numero="{{ config('site.whatsapp') }}" data-intro="{{ $servico['message'] }}">
  <div class="quest-topo">
    <span class="tag">{{ $servico['title'] }}</span>
    <h1 class="h2">Conte um pouco sobre você</h1>
    <p class="lead">São 10 perguntas rápidas. No final, as respostas vão prontas para o WhatsApp do Denilson, que faz o orçamento e monta o seu treino a partir delas.</p>
  </div>

  <form class="quest-form" novalidate data-quest>
    <div class="quest-item" data-q-nome>
      <label class="quest-rotulo" for="quest-nome">Seu nome</label>
      <input class="quest-campo" id="quest-nome" name="nome" type="text" maxlength="80" autocomplete="given-name" placeholder="Como você quer ser chamado(a)">
      <p class="quest-erro" hidden>Escreva o seu nome.</p>
    </div>

    @foreach (config('site.questionario') as $i => $q)
      <fieldset class="quest-item" data-q data-rotulo="{{ $q['rotulo'] }}" data-tipo="{{ $q['tipo'] }}" @if (empty($q['opcional'])) data-obrigatoria @endif>
        <legend class="quest-rotulo"><span class="quest-num">{{ $i + 1 }}</span><span>{{ $q['pergunta'] }}@if (! empty($q['opcional'])) <em>(opcional)</em>@endif</span></legend>
        @if (! empty($q['ajuda']))<p class="quest-ajuda">{{ $q['ajuda'] }}</p>@endif

        @switch($q['tipo'])
          @case('opcoes')
          @case('varias')
            <div class="quest-opcoes">
              @foreach ($q['opcoes'] as $op)
                <label class="quest-op"><input type="{{ $q['tipo'] === 'varias' ? 'checkbox' : 'radio' }}" name="q{{ $i }}" value="{{ $op }}"><span>{{ $op }}</span></label>
              @endforeach
            </div>
            @break
          @case('medidas')
            <div class="quest-medidas">
              <label>Idade<input class="quest-campo" name="q{{ $i }}_idade" type="text" inputmode="numeric" maxlength="3" placeholder="34" data-unidade="anos"></label>
              <label>Peso (kg)<input class="quest-campo" name="q{{ $i }}_peso" type="text" inputmode="decimal" maxlength="5" placeholder="72" data-unidade="kg"></label>
              <label>Altura (m)<input class="quest-campo" name="q{{ $i }}_altura" type="text" inputmode="decimal" maxlength="5" placeholder="1,65" data-unidade="m"></label>
            </div>
            @break
          @case('sim_qual')
            <div class="quest-opcoes">
              <label class="quest-op"><input type="radio" name="q{{ $i }}" value="Não"><span>Não</span></label>
              <label class="quest-op"><input type="radio" name="q{{ $i }}" value="Sim"><span>Sim</span></label>
            </div>
            <input class="quest-campo quest-qual" name="q{{ $i }}_qual" type="text" maxlength="200" placeholder="Qual? Conte em poucas palavras" hidden data-qual>
            @break
          @default
            <textarea class="quest-campo" name="q{{ $i }}" rows="3" maxlength="600" placeholder="Ex.: gosto de agachamento, não gosto de correr, treino de manhã..."></textarea>
        @endswitch
        <p class="quest-erro" hidden>{{ $q['tipo'] === 'medidas' ? 'Informe pelo menos a sua idade.' : 'Responda esta pergunta.' }}</p>
      </fieldset>
    @endforeach

    <div class="quest-lgpd" data-q-aceite>
      <p><b>{{ $lgpd['titulo'] }}.</b> {{ $lgpd['texto'] }}</p>
      <label class="quest-aceite"><input type="checkbox" name="aceite"><span>{{ $lgpd['consentimento'] }}</span></label>
      <p class="quest-erro" hidden>Marque a concordância para enviar.</p>
    </div>

    <button class="btn btn-d btn-lg quest-enviar" type="submit">@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 20, 'weight' => 2.2, 'class' => 'ic-acc'])Enviar pelo WhatsApp</button>
    <p class="quest-enviado" role="status" hidden data-enviado>Pronto! Se o WhatsApp não abriu, <a href="#" target="_blank" rel="noopener" data-enviado-link>toque aqui para abrir</a>.</p>
  </form>
</section>

@endsection
