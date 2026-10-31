@php
  // Ícones de traço do layout (24x24). Uso: @include('partials.icons.icon', ['name' => 'halter', 'size' => 26])
  $paths = [
      'whatsapp' => '<path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 21l2.1-5.4A8.4 8.4 0 1 1 21 11.5z"></path>',
      'seta' => '<path d="M5 12h14M13 6l6 6-6 6"></path>',
      'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"></path>',
      'menu' => '<path d="M4 7h16M4 12h16M4 17h16"></path>',
      'fechar' => '<path d="M6 6l12 12M18 6L6 18"></path>',
      'halter' => '<path d="M6.5 6.5v11M17.5 6.5v11M3 9.5v5M21 9.5v5M6.5 12h11"></path>',
      'folha' => '<path d="M12 21c-4.4 0-8-3.6-8-8 0-5 4-9 8-10 4 1 8 5 8 10 0 4.4-3.6 8-8 8z"></path><path d="M12 21V9"></path>',
      'coracao' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8z"></path>',
      'trofeu' => '<path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0z"></path><path d="M17 5h3v2a3 3 0 0 1-3 3M7 5H4v2a3 3 0 0 0 3 3"></path>',
      'celular' => '<rect x="5" y="2" width="14" height="20" rx="2.5"></rect><path d="M9 7h6M9 11h6M9 15h3"></path>',
      'local' => '<path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"></path><circle cx="12" cy="10" r="2.5"></circle>',
      'lista' => '<path d="M4 6h16M4 12h16M4 18h10"></path>',
      'relogio' => '<circle cx="12" cy="13" r="8"></circle><path d="M12 9v4l2.5 2.5M9 2h6"></path>',
      'execucao' => '<path d="M9 5H5v4M15 5h4v4M9 19H5v-4M15 19h4v-4"></path><path d="M10 12h4"></path>',
  ];
@endphp
<svg @isset($class) class="{{ $class }}" @endisset width="{{ $size ?? 20 }}" height="{{ $size ?? 20 }}" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke ?? 'currentColor' }}" stroke-width="{{ $weight ?? 2 }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] !!}</svg>
