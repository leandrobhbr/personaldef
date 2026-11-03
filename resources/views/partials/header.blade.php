@php
  $links = [
      ['label' => 'Home', 'href' => route('home').'#home'],
      ['label' => 'Sobre', 'href' => route('home').'#sobre'],
      ['label' => 'Serviços', 'href' => route('home').'#servicos'],
      ['label' => 'Como Funciona', 'href' => route('home').'#como-funciona'],
      ['label' => 'Fichas', 'href' => route('home').'#fichas'],
      ['label' => 'Blog', 'href' => route('home').'#blog'],
      ['label' => 'Contato', 'href' => route('home').'#contato'],
  ];
  $ctaUrl = $wa('Olá Denilson! Quero saber mais sobre o acompanhamento.');
@endphp
<header class="hdr">
  <div class="wrap" style="padding-top: 16px; padding-bottom: 16px; display: flex; align-items: center; justify-content: space-between; gap: 24px">
    <a href="{{ route('home') }}" aria-label="Personal DEF — início" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--fg)">
      <span class="logo">DEF</span>
      <span style="display: flex; flex-direction: column; line-height: 1.1">
        <span style="font-family: Montserrat, sans-serif; font-weight: 800; font-size: 18px">Personal DEF</span>
        <span style="font-size: 12px; color: #55585E">{{ config('site.owner_name') }} · Personal Trainer</span>
      </span>
    </a>
    <nav class="nav-desk" aria-label="Principal">
      @foreach ($links as $link)
        <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
      @endforeach
    </nav>
    <a class="btn btn-o app-top" href="{{ url('/app/') }}" aria-label="Abrir o aplicativo de fichas">@include('partials.icons.icon', ['name' => 'celular', 'size' => 18])<span>Aplicativo</span></a>
    <a class="btn btn-d cta-desk" href="{{ $ctaUrl }}" target="_blank" rel="noopener" data-wa-open style="min-height: 44px; padding: 0 20px; font-weight: 700">@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 18, 'class' => 'ic-acc'])Falar no WhatsApp</a>
    <button type="button" class="burger" data-menu-toggle aria-label="Abrir menu" aria-expanded="false" aria-controls="menu-mobile">
      <span data-icon="open">@include('partials.icons.icon', ['name' => 'menu', 'size' => 22])</span>
      <span data-icon="close" hidden>@include('partials.icons.icon', ['name' => 'fechar', 'size' => 22])</span>
    </button>
  </div>
  <nav id="menu-mobile" class="mnav" aria-label="Menu mobile" hidden>
    @foreach ($links as $link)
      <a class="lnk" href="{{ $link['href'] }}">{{ $link['label'] }}</a>
    @endforeach
    <a class="btn btn-o" href="{{ url('/app/') }}" style="margin-top: 12px; min-height: 52px">@include('partials.icons.icon', ['name' => 'celular', 'size' => 18])Aplicativo de fichas</a>
    <a class="btn btn-d" href="{{ $ctaUrl }}" target="_blank" rel="noopener" data-wa-open style="margin-top: 8px; min-height: 52px">Falar no WhatsApp</a>
  </nav>
</header>
