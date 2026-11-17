{{--
  Questionário dos serviços em modal, uma pergunta por vez (abre pelos botões [data-quest-open="online|presencial"]
  ou por /?questionario=online|presencial). Nada é salvo no site: o site.js monta a mensagem e abre o WhatsApp.
  Perguntas e aviso de LGPD em config/site.php.
--}}
@php
  $qmServicos = collect(config('site.servicos'))->mapWithKeys(fn ($s) => [$s['tipo'] => ['titulo' => $s['title'], 'intro' => $s['message']]]);
  $qmLgpd = config('site.questionario_lgpd');
  $qmPerguntas = config('site.questionario');
@endphp
<div class="wam qm" id="quest-modal" data-numero="{{ config('site.whatsapp') }}" data-servicos="{{ json_encode($qmServicos, JSON_UNESCAPED_UNICODE) }}" hidden>
  <div class="wam-backdrop" data-qm-fechar></div>
  <div class="wam-panel qm-panel" role="dialog" aria-modal="true" aria-labelledby="qm-titulo">
    <div class="wam-head">
      <div>
        <p class="qm-servico" data-qm-servico>{{ $qmServicos['online']['titulo'] ?? '' }}</p>
        <h2 id="qm-titulo" class="qm-titulo">Orçamento em 1 minuto</h2>
      </div>
      <button type="button" class="wam-close" data-qm-fechar aria-label="Fechar">@include('partials.icons.icon', ['name' => 'fechar', 'size' => 20, 'weight' => 2.5])</button>
    </div>

    <div class="qm-progresso" aria-hidden="true"><i data-qm-barra></i></div>
    <p class="qm-contador" data-qm-contador aria-live="polite"></p>

    <form class="qm-form" novalidate data-quest>
      <div class="qm-passo" data-passo data-q-nome>
        <label class="quest-rotulo" for="qm-nome">Para começar, como você se chama?</label>
        <input class="quest-campo" id="qm-nome" name="nome" type="text" maxlength="80" autocomplete="given-name" placeholder="Seu nome">
        <p class="quest-erro" hidden>Escreva o seu nome.</p>
      </div>

      @foreach ($qmPerguntas as $i => $q)
        @php($curtas = in_array($q['tipo'], ['opcoes', 'varias']) && max(array_map('mb_strlen', $q['opcoes'])) <= 8)
        <fieldset class="qm-passo" data-passo data-q data-rotulo="{{ $q['rotulo'] }}" data-tipo="{{ $q['tipo'] }}" @if (empty($q['opcional'])) data-obrigatoria @endif hidden>
          <legend class="quest-rotulo">{{ $q['pergunta'] }}@if (! empty($q['opcional'])) <em>(opcional)</em>@endif</legend>
          @if (! empty($q['ajuda']))<p class="quest-ajuda">{{ $q['ajuda'] }}</p>@endif

          @switch($q['tipo'])
            @case('opcoes')
            @case('varias')
              <div class="quest-opcoes {{ $curtas ? 'quest-opcoes--linha' : '' }}">
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
              <div class="quest-opcoes quest-opcoes--linha">
                <label class="quest-op"><input type="radio" name="q{{ $i }}" value="Não"><span>Não</span></label>
                <label class="quest-op"><input type="radio" name="q{{ $i }}" value="Sim"><span>Sim</span></label>
              </div>
              <input class="quest-campo quest-qual" name="q{{ $i }}_qual" type="text" maxlength="200" placeholder="Qual? Conte em poucas palavras" hidden data-qual>
              @break
            @default
              <textarea class="quest-campo" name="q{{ $i }}" rows="3" maxlength="600" placeholder="Ex.: gosto de agachamento, não gosto de correr, treino de manhã..."></textarea>
          @endswitch
          <p class="quest-erro" hidden>{{ $q['tipo'] === 'medidas' ? 'Informe pelo menos a sua idade.' : 'Responda para continuar.' }}</p>
        </fieldset>
      @endforeach

      <div class="qm-passo" data-passo data-q-aceite hidden>
        <p class="quest-rotulo">Pronto! Antes de enviar:</p>
        <div class="quest-lgpd">
          <p><b>{{ $qmLgpd['titulo'] }}.</b> {{ $qmLgpd['texto'] }}</p>
          <label class="quest-aceite"><input type="checkbox" name="aceite"><span>{{ $qmLgpd['consentimento'] }}</span></label>
        </div>
        <p class="quest-erro" hidden>Marque a concordância para enviar.</p>
      </div>

      <div class="qm-enviado" role="status" hidden data-enviado>
        <span class="ibox ibox-acc">@include('partials.icons.icon', ['name' => 'check', 'size' => 26, 'weight' => 3])</span>
        <p><b>Respostas prontas no WhatsApp!</b> É só enviar a mensagem para o Denilson. Se o WhatsApp não abriu, <a href="#" target="_blank" rel="noopener" data-enviado-link>toque aqui</a>.</p>
      </div>

      <div class="qm-acoes" data-qm-acoes>
        <button type="button" class="btn qm-voltar" data-qm-voltar>Voltar</button>
        <button type="button" class="btn btn-d" data-qm-proxima>Próxima</button>
        <button type="submit" class="btn btn-d" data-qm-enviar hidden>@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 20, 'weight' => 2.2, 'class' => 'ic-acc'])Enviar pelo WhatsApp</button>
      </div>
    </form>
  </div>
</div>
