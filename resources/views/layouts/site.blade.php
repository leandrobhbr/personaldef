<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
@include('partials.analytics')
@include('partials.seo')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
  <style>:root{--accent:{{ config('site.accent') }}}</style>
  <!-- Animações ao rolar: esconde os blocos só quando o JS está ativo (e mostra tudo se o site.js não carregar em 3s) -->
  <script>if(!matchMedia('(prefers-reduced-motion: reduce)').matches){document.documentElement.classList.add('reveal');setTimeout(function(){if(!window.revealReady)document.documentElement.classList.remove('reveal')},3000)}</script>
</head>
<body>

@include('partials.header')

<main>
@yield('content')
</main>

@include('partials.footer')

<!-- WhatsApp flutuante -->
<a class="wa-float" href="{{ $wa() }}" target="_blank" rel="noopener" data-wa-open aria-label="Falar no WhatsApp">@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 28, 'weight' => 2.2])</a>

@include('partials.whatsapp-modal')

<!-- Voltar ao topo -->
<button type="button" class="to-top" data-to-top aria-label="Voltar ao topo"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"></path></svg></button>

<script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}" defer></script>
</body>
</html>
