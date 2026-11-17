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
      'fotos' => '<rect x="3" y="5" width="18" height="14" rx="2"></rect><circle cx="9" cy="10" r="1.6"></circle><path d="M21 16l-5-5-8 8"></path>',
      'anterior' => '<path d="M15 5l-7 7 7 7"></path>',
      'proxima' => '<path d="M9 5l7 7-7 7"></path>',
      'execucao' => '<path d="M9 5H5v4M15 5h4v4M9 19H5v-4M15 19h4v-4"></path><path d="M10 12h4"></path>',
      // concursos (símbolos genéricos, sem brasão de nenhuma corporação)
      'escudo' => '<path d="M12 3l7 3v5.5c0 4.3-2.9 8-7 9.5-4.1-1.5-7-5.2-7-9.5V6z"></path><path d="M12 8.2l1.2 2.4 2.6.4-1.9 1.8.5 2.6L12 14.2l-2.4 1.2.5-2.6-1.9-1.8 2.6-.4z"></path>',
      'distintivo' => '<circle cx="12" cy="12" r="9"></circle><path d="M12 6.8l1.6 3.3 3.6.5-2.6 2.5.6 3.6L12 15l-3.2 1.7.6-3.6-2.6-2.5 3.6-.5z"></path>',
      'rodovia' => '<path d="M9 3L5 21M15 3l4 18"></path><path d="M12 4v2.5M12 10.5v3M12 17.5V20"></path>',
      'grade' => '<rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M9 3v18M15 3v18M4 10h16"></path>',
      'chama' => '<path d="M12 22c-3.9 0-7-2.9-7-6.7 0-3 1.8-4.9 3.3-6.4.3 1.6 1.2 2.7 2.4 3.2C10.3 8.3 11.9 5 15 2.5c-.3 3 1 5 2.3 6.7 1 1.4 1.7 3 1.7 5.1 0 4.1-3.1 7.7-7 7.7z"></path><path d="M12 22c-1.7 0-3-1.3-3-3 0-1.6 1.2-2.7 2-3.5.3 1 .9 1.6 1.6 1.9.1-1.2.6-2.4 1.6-3.2.3 1.5 1.8 2.6 1.8 4.6 0 1.8-1.4 3.2-4 3.2z"></path>',
      'predio' => '<path d="M3 21h18M5 21V10M19 21V10M9 21v-7M15 21v-7M3 10l9-6 9 6z"></path>',
      'estrela' => '<path d="M12 2.8l2.8 5.7 6.3.9-4.6 4.4 1.1 6.2L12 17.1 6.4 20l1.1-6.2-4.6-4.4 6.3-.9z"></path>',
      'prancheta' => '<rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V3h6v1M9 10h6M9 14h6M9 18h3"></path>',
  ];
@endphp
<svg @isset($class) class="{{ $class }}" @endisset width="{{ $size ?? 20 }}" height="{{ $size ?? 20 }}" viewBox="0 0 24 24" fill="none" stroke="{{ $stroke ?? 'currentColor' }}" stroke-width="{{ $weight ?? 2 }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] !!}</svg>
