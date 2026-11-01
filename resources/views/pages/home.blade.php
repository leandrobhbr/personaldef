@extends('layouts.site')

@push('schema')
  <script type="application/ld+json">{!! json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'HealthAndBeautyBusiness',
      'name' => 'Personal DEF',
      'description' => 'Consultoria de treino online para todo o Brasil e personal trainer presencial em Marília/SP, com formação europeia.',
      'url' => url('/'),
      'founder' => ['@type' => 'Person', 'name' => config('site.owner_name'), 'jobTitle' => 'Personal Trainer'],
      'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Marília', 'addressRegion' => 'SP', 'addressCountry' => 'BR'],
      'areaServed' => 'BR',
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')

<!-- HERO -->
<section id="home" class="wrap hero">
  <div style="flex: 1 1 520px; min-width: 0; display: flex; flex-direction: column; gap: 28px">
    <span class="pill"><i></i><span>20+ anos de carreira · Formação na Europa e no Brasil</span></span>
    <h1>Transforme seu corpo e sua saúde com acompanhamento de <span class="mark">padrão europeu</span>.</h1>
    <p style="font-size: 19px; color: #4A4C52; max-width: 580px">Dois caminhos, o mesmo método: <strong>Consultoria de Treino Online</strong> com fichas 100% individualizadas para todo o Brasil, ou <strong>Personal Trainer Presencial</strong> exclusivo em {{ config('site.city') }}.</p>
    <div style="display: flex; flex-wrap: wrap; gap: 14px">
      <a class="btn btn-d btn-lg" href="{{ $wa('Olá Denilson! Quero iniciar minha transformação.') }}" target="_blank" rel="noopener" data-wa-open>@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 20, 'weight' => 2.2, 'class' => 'ic-acc'])Iniciar Minha Transformação</a>
      <a class="btn btn-o btn-lg" href="#servicos">Ver Planos @include('partials.icons.icon', ['name' => 'seta', 'size' => 18])</a>
    </div>
    <div class="stats">
      <div><b>7 anos</b><span>de formação na Europa</span></div>
      <div><b>Online</b><span>para todo o Brasil</span></div>
      <div><b>Presencial</b><span>em {{ config('site.city') }}</span></div>
    </div>
  </div>
  <div style="flex: 1 1 400px; min-width: 0; display: flex; justify-content: center">
    <div class="photo">
      <div class="photo-sun"></div>
      @if (config('site.owner_photo'))
        <img src="{{ asset(config('site.owner_photo')) }}" alt="{{ config('site.owner_name') }}, personal trainer" width="356" height="464" style="object-position: 50% 30%">
      @else
        <svg role="img" aria-label="Espaço para a foto profissional de {{ config('site.owner_name') }}" width="72%" viewBox="0 0 200 240" fill="none" style="display: block; position: relative"><circle cx="100" cy="70" r="40" fill="#C9C9C2"></circle><path d="M20 240c0-54 36-96 80-96s80 42 80 96z" fill="#C9C9C2"></path></svg>
        <div class="photo-note">[Foto profissional — {{ config('site.owner_name') }}]</div>
      @endif
      <div class="photo-card">
        <span style="display: flex; flex-direction: column; line-height: 1.3"><strong style="font-size: 15px">{{ config('site.owner_name') }}</strong><span style="font-size: 13px; color: #55585E">{{ config('site.owner_role') }}</span></span>
      </div>
    </div>
  </div>
</section>

<!-- SOBRE -->
<section id="sobre" style="background: #FFFFFF; border-top: 1px solid #ECECE6; border-bottom: 1px solid #ECECE6">
  <div class="wrap sec" style="display: flex; flex-direction: column; gap: 56px">
    <div style="display: flex; flex-wrap: wrap; gap: 48px; align-items: flex-start">
      <div style="flex: 1 1 380px; min-width: 0; display: flex; flex-direction: column; gap: 16px">
        <span class="tag">Sobre o Personal</span>
        <h2 class="h2">Conhecimento lapidado nas melhores escolas fitness da Europa.</h2>
      </div>
      <div style="flex: 1 1 480px; min-width: 0; display: flex; flex-direction: column; gap: 18px; font-size: 17px; color: #4A4C52">
        <p>Durante <strong>7 anos vivendo na Europa</strong>, Denilson Ferreira mergulhou nas melhores escolas e ginásios fitness da <strong>Espanha e de Portugal</strong> para aprimorar seus conhecimentos em atividade física e nutrição — aprendendo, na prática, como os grandes centros europeus transformam corpos com método, ciência e consistência.</p>
        <p>De volta ao Brasil, ele trouxe esse padrão para cada aluno: um acompanhamento que une treino, alimentação e bem-estar em uma estratégia só sua. Hoje atua como <strong>Personal Trainer registrado no CREF {{ config('site.cref') }}</strong>.</p>
      </div>
    </div>
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(min(420px, 100%), 1fr)); gap: 24px">
      @foreach (config('site.europa') as $foto)
        <figure class="trip">
          <img src="{{ asset($foto['image']) }}" alt="{{ $foto['alt'] }}" width="1000" height="700" loading="lazy">
          <figcaption class="trip-label"><i></i><span><strong>{{ $foto['country'] }}</strong> · {{ $foto['city'] }}</span></figcaption>
          <small class="trip-credit">Foto: <a href="{{ $foto['source'] }}" target="_blank" rel="noopener">{{ $foto['author'] }}</a> · <a href="{{ $foto['license_url'] }}" target="_blank" rel="noopener">{{ $foto['license'] }}</a></small>
        </figure>
      @endforeach
    </div>
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(min(260px, 100%), 1fr))">
      @foreach (config('site.formacao') as $item)
        <article class="card">
          <span class="ibox">@include('partials.icons.icon', ['name' => $item['icon'], 'size' => 26])</span>
          <span class="kicker">{{ $item['tag'] }}</span>
          <h3>{{ $item['title'] }}</h3>
          <p>{{ $item['where'] }}</p>
        </article>
      @endforeach
    </div>
  </div>
</section>

<!-- SERVIÇOS -->
<section id="servicos" class="wrap sec" style="display: flex; flex-direction: column; gap: 48px">
  <div style="display: flex; flex-wrap: wrap; gap: 40px 48px; align-items: center">
    <div style="flex: 1 1 420px; min-width: 0; display: flex; flex-direction: column; gap: 14px">
      <span class="tag">Serviços e Planos</span>
      <h2 class="h2">Escolha como você quer treinar.</h2>
      <p class="lead">Online ou presencial, o ponto de partida é sempre o mesmo: entender você antes de montar qualquer exercício.</p>
    </div>
    <figure class="gym">
      <img src="{{ asset('images/academia.webp') }}" alt="Sala de musculação com bancos, racks e barras" width="1100" height="860" loading="lazy">
      <small class="trip-credit">Foto: <a href="https://commons.wikimedia.org/wiki/File:GymNation_gym_interior_2024.jpg" target="_blank" rel="noopener">GymNation</a> · <a href="https://creativecommons.org/licenses/by-sa/4.0/deed.pt-br" target="_blank" rel="noopener">CC BY-SA 4.0</a></small>
    </figure>
  </div>
  <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(min(320px, 100%), 1fr)); gap: 24px">
    @foreach (config('site.servicos') as $servico)
      <article class="plan">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap">
          <span class="ibox ibox-acc">@include('partials.icons.icon', ['name' => $servico['icon'], 'size' => 28])</span>
          <span class="badge">{{ $servico['badge'] }}</span>
        </div>
        <h3>{{ $servico['title'] }}</h3>
        <p style="font-size: 16px; color: #4A4C52">{{ $servico['text'] }}</p>
        <ul class="checks">
          @foreach ($servico['items'] as $li)
            <li><span>@include('partials.icons.icon', ['name' => 'check', 'size' => 14, 'weight' => 3])</span>{{ $li }}</li>
          @endforeach
        </ul>
        <div class="plan-foot">
          <a class="btn btn-d" href="{{ $wa($servico['message']) }}" target="_blank" rel="noopener" data-wa-open data-wa-tipo="{{ $servico['tipo'] }}">{{ $servico['cta'] }}</a>
        </div>
      </article>
    @endforeach
  </div>
</section>

<!-- COMO FUNCIONA -->
<section id="como-funciona" style="background: #F1F1EC; border-top: 1px solid #E4E4DE; border-bottom: 1px solid #E4E4DE">
  <div class="wrap sec" style="display: flex; flex-direction: column; gap: 56px">
    <div style="display: flex; flex-wrap: wrap; gap: 48px; align-items: flex-end">
      <div style="flex: 1 1 460px; min-width: 0; display: flex; flex-direction: column; gap: 14px">
        <span class="tag">Como funciona a consultoria</span>
        <h2 class="h2">Sua ficha de treino, do zero, feita só para você.</h2>
      </div>
      <p class="lead" style="flex: 1 1 420px; min-width: 0; color: #3B3D42">O treino é <strong>100% individualizado e montado do zero</strong> de acordo com a sua necessidade, rotina, limitações e objetivos — <strong>emagrecimento, hipertrofia ou saúde</strong>.</p>
    </div>
    <ol class="steps">
      @foreach (config('site.passos') as $i => [$titulo, $texto])
        <li>
          <b>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</b>
          <h3>{{ $titulo }}</h3>
          <p>{{ $texto }}</p>
        </li>
      @endforeach
    </ol>
    <div class="ficha">
      <div style="flex: 1 1 420px; min-width: 0; display: flex; flex-direction: column; gap: 20px">
        <h3 style="font-weight: 800; font-size: 26px; line-height: 1.2">O que vem na sua ficha</h3>
        <p style="font-size: 16px; color: #4A4C52">Cada exercício é explicado para que você saiba exatamente como treinar de forma segura — mesmo sem um personal ao lado. A ficha chega no celular, pronta para acompanhar o treino série por série.</p>
        <div class="grid ficha-grid">
          @foreach (config('site.ficha') as [$icon, $label])
            <div class="ficha-item">@include('partials.icons.icon', ['name' => $icon, 'size' => 22])<span>{{ $label }}</span></div>
          @endforeach
        </div>
      </div>
      @include('partials.ficha-celular')
    </div>
  </div>
</section>

<!-- TIPOS DE FICHA -->
<section id="fichas" style="background: #FFFFFF; border-bottom: 1px solid #E4E4DE">
  <div class="wrap sec" style="display: flex; flex-direction: column; gap: 48px">
    <div style="display: flex; flex-direction: column; gap: 14px; max-width: 720px">
      <span class="tag">Tipos de ficha</span>
      <h2 class="h2">Qual divisão de treino combina com você?</h2>
      <p class="lead">Na avaliação, Denilson escolhe a divisão ideal para a sua rotina e o seu objetivo.</p>
    </div>
    <div class="tipos">
      @foreach (config('site.tipos_ficha') as $tipo)
        <article class="tipo">
          <div class="tipo-dias">
            @foreach ($tipo['days'] as [$letra, $grupos, $foto, $pos])
              <figure class="dia">
                <img src="{{ asset($foto) }}" alt="Treino de {{ mb_strtolower($grupos) }}" loading="lazy" style="object-position: {{ $pos }}">
                <figcaption><b>{{ $letra }}</b><span>{{ $grupos }}</span></figcaption>
              </figure>
            @endforeach
          </div>
          <div style="padding: 28px; display: flex; flex-direction: column; gap: 14px; flex-grow: 1">
            <div>
              <h3>{{ $tipo['title'] }}</h3>
              <span class="kicker">{{ $tipo['subtitle'] }}</span>
            </div>
            <p style="font-size: 15px; color: #4A4C52">{{ $tipo['what'] }}</p>
            <dl class="tipo-info">
              <div><dt>Frequência</dt><dd>{{ $tipo['freq'] }}</dd></div>
              <div><dt>Indicado para</dt><dd>{{ $tipo['who'] }}</dd></div>
            </dl>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>

<!-- SAÚDE: FICHA INDIVIDUALIZADA -->
<section id="saude" class="wrap sec" style="display: flex; flex-direction: column; gap: 48px">
  <div style="display: flex; flex-wrap: wrap; gap: 48px; align-items: flex-end">
    <div style="flex: 1 1 460px; min-width: 0; display: flex; flex-direction: column; gap: 14px">
      <span class="tag">Treino e saúde</span>
      <h2 class="h2">Ficha padrão não serve para quem tem uma condição de saúde.</h2>
    </div>
    <div style="flex: 1 1 420px; min-width: 0; display: flex; flex-direction: column; gap: 24px">
      <p class="lead">Nesses casos, um treino genérico pode agravar sintomas e causar lesões. A ficha <strong>individualizada</strong> garante segurança e resultado.</p>
      @if (config('site.owner_photo'))
        <div class="fala">
          <img src="{{ asset(config('site.owner_photo')) }}" alt="{{ config('site.owner_name') }}" width="72" height="72" loading="lazy">
          <p class="fala-balao"><strong>Estou aqui para te ajudar!</strong><span>{{ config('site.owner_name') }} · CREF {{ config('site.cref') }}</span></p>
        </div>
      @endif
    </div>
  </div>

  <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(min(520px, 100%), 1fr)); gap: 24px">
    @foreach (config('site.saude') as $i => $grupo)
      <article class="cond">
        <div class="cond-img">
          <img src="{{ asset($grupo['image']) }}" alt="" loading="lazy" style="object-position: {{ $grupo['pos'] }}">
          <b>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</b>
        </div>
        <div class="cond-body">
          <h3>{{ $grupo['title'] }}</h3>
          <p class="cond-intro">{{ $grupo['intro'] }}</p>
          <ul class="cond-list">
            @foreach ($grupo['items'] as [$nome, $texto])
              <li><strong>{{ $nome }}:</strong> {{ $texto }}</li>
            @endforeach
          </ul>
        </div>
      </article>
    @endforeach
  </div>

  <div class="sinais">
    <div class="sinal sinal-ok">
      <span class="ibox ibox-acc" style="width: 48px; height: 48px; border-radius: 14px">@include('partials.icons.icon', ['name' => 'check', 'size' => 24, 'weight' => 2.6])</span>
      <h3>Fadiga muscular normal</h3>
      <p>Queimação no músculo durante o treino e dor suportável de 24 a 48 horas depois.</p>
    </div>
    <div class="sinal sinal-alerta">
      <span class="ibox" style="width: 48px; height: 48px; border-radius: 14px; background: var(--accent); color: var(--ink)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0zM12 9v4M12 17h.01"></path></svg></span>
      <h3>Sinal de alerta: pare o exercício</h3>
      <p>Dor aguda em articulação, tendão ou osso; dormência, formigamento ou inchaço.</p>
    </div>
  </div>

  <div class="saude-cta">
    @if (config('site.owner_photo'))
      <img class="saude-foto" src="{{ asset(config('site.owner_photo')) }}" alt="{{ config('site.owner_name') }}" width="112" height="112" loading="lazy">
    @endif
    <div style="flex: 1 1 380px; min-width: 0; display: flex; flex-direction: column; gap: 14px">
      <h3>Tem alguma dessas condições? Conte para o Denilson:</h3>
      <ol>
        <li>Seu diagnóstico ou onde sente dor.</li>
        <li>Restrições passadas pelo seu médico.</li>
        <li>Seu objetivo: fortalecer, emagrecer ou ganhar mobilidade.</li>
      </ol>
    </div>
    <a class="btn btn-d btn-lg" href="{{ $wa('Olá Denilson! Tenho uma condição de saúde e quero uma ficha individualizada. Meu diagnóstico/região da dor: ... Restrição médica: ... Meu objetivo: ...') }}" target="_blank" rel="noopener" data-wa-open data-wa-obs="Minha condição ou região da dor: &#10;Restrição do médico: &#10;Meu objetivo: ">@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 20, 'weight' => 2.2, 'class' => 'ic-acc'])Contar pelo WhatsApp</a>
  </div>

</section>

<!-- DEPOIMENTOS (exemplos só aparecem fora de produção ou na prévia com senha; veja config/site.php) -->
@php
  $previa = session('previa', false);
  $depoimentos = collect(config('site.depoimentos'))->reject(fn ($d) => ! empty($d['exemplo']) && app()->isProduction() && ! $previa);
@endphp
@if ($depoimentos->isNotEmpty())
<section id="depoimentos" class="wrap sec" style="display: flex; flex-direction: column; gap: 48px">
  <div style="display: flex; flex-direction: column; gap: 14px; max-width: 640px">
    <span class="tag">Depoimentos</span>
    <h2 class="h2">Quem treina com o DEF aprova.</h2>
    @if ($previa)
      <p class="previa-faixa">Modo prévia · depoimentos ilustrativos, visíveis só para quem tem a senha · <a href="{{ route('previa.sair') }}">Sair da prévia</a></p>
    @endif
  </div>
  <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr)); gap: 24px">
    @foreach ($depoimentos as $depoimento)
      <figure class="depo">
        <div class="depo-estrelas" role="img" aria-label="Nota 5 de 5">
          @foreach (range(1, 5) as $estrela)
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6-4.9-4.6 6.6-.8z" fill="currentColor"></path></svg>
          @endforeach
        </div>
        <blockquote><p>“{{ $depoimento['texto'] }}”</p></blockquote>
        @if (! empty($depoimento['fotos']))
          @php
            $galeria = collect($depoimento['fotos'])->map(fn ($f) => ['src' => asset($f).'?v='.@filemtime(public_path($f)), 'alt' => $depoimento['contexto']])->values();
          @endphp
          <button type="button" class="depo-ver" data-galeria="{{ $galeria->toJson(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}" data-galeria-titulo="{{ $depoimento['nome'] }} · {{ $depoimento['contexto'] }}">
            @include('partials.icons.icon', ['name' => 'fotos', 'size' => 18])
            Veja fotos
          </button>
        @endif
        <figcaption>
          @if (! empty($depoimento['foto']))
            <img class="depo-foto" src="{{ asset($depoimento['foto']) }}" alt="{{ $depoimento['nome'] }}" width="56" height="56" loading="lazy">
          @else
            <span class="depo-foto depo-iniciais" aria-hidden="true">{{ collect(explode(' ', $depoimento['nome']))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}</span>
          @endif
          <span>
            <strong>{{ $depoimento['nome'] }}</strong>
            <small>{{ $depoimento['contexto'] }}</small>
          </span>
        </figcaption>
      </figure>
    @endforeach
  </div>
</section>

@include('partials.galeria-modal')
@endif

<!-- BLOG -->
<section id="blog" class="wrap sec" style="display: flex; flex-direction: column; gap: 48px">
  <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-end; justify-content: space-between">
    <div style="display: flex; flex-direction: column; gap: 14px; max-width: 640px">
      <span class="tag">Blog DEF</span>
      <h2 class="h2">Treino, nutrição e saúde sem enrolação.</h2>
    </div>
    <a href="#blog" style="display: inline-flex; align-items: center; gap: 8px; min-height: 44px; font-weight: 700; font-size: 15px; text-decoration: none">Ver todos os artigos @include('partials.icons.icon', ['name' => 'seta', 'size' => 18])</a>
  </div>
  <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(min(300px, 100%), 1fr)); gap: 24px">
    @foreach (config('site.posts') as $post)
      <article class="post">
        <img class="post-img" src="{{ asset($post['image']) }}" alt="" width="1200" height="630" loading="lazy">
        <div class="post-body">
          <span class="kicker">{{ $post['category'] }} · {{ $post['reading'] }} min de leitura</span>
          <h3>{{ $post['title'] }}</h3>
          <p>{{ $post['excerpt'] }}</p>
          <a class="btn btn-o" href="#blog">Ler Artigo</a>
        </div>
      </article>
    @endforeach
  </div>
</section>

<!-- CTA FINAL / CONTATO -->
<section id="contato" class="wrap" style="padding-bottom: 96px">
  <div class="cta">
    <div style="flex: 1 1 480px; min-width: 0; display: flex; flex-direction: column; gap: 12px">
      <h2>Sua transformação começa com uma mensagem.</h2>
      <p style="font-size: 18px; color: #1E1F22">Chame no WhatsApp e agende sua conversa inicial em poucos segundos — online para todo o Brasil ou presencial em {{ config('site.city') }}.</p>
    </div>
    <a class="btn btn-d" href="{{ $wa('Olá Denilson! Quero agendar minha conversa inicial.') }}" target="_blank" rel="noopener" data-wa-open style="min-height: 60px; padding: 0 32px; font-size: 17px; gap: 12px">@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 22, 'weight' => 2.2, 'class' => 'ic-acc'])Agendar pelo WhatsApp</a>
  </div>
</section>

@endsection
