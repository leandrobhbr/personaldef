@php
  $seoTitle = View::hasSection('title') ? trim(View::getSection('title')).' | Personal DEF' : 'Personal DEF — Denilson Ferreira · Personal Trainer em Marília/SP';
  $seoDescription = trim(View::getSection('description', 'Consultoria de treino online para todo o Brasil e personal trainer presencial em Marília/SP, com formação europeia.'));
  $seoUrl = url()->current();
@endphp
  <title>{!! $seoTitle !!}</title>
  <meta name="description" content="{!! $seoDescription !!}">
  <meta name="robots" content="@yield('robots', 'index,follow,max-image-preview:large')">
  <link rel="canonical" href="{{ $seoUrl }}">
  <meta name="theme-color" content="#0E0F11">
  <meta name="author" content="{{ config('site.owner_name') }}">
@if (config('site.google_verification'))
  <meta name="google-site-verification" content="{{ config('site.google_verification') }}">
@endif

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Personal DEF">
  <meta property="og:locale" content="pt_BR">
  <meta property="og:title" content="{!! $seoTitle !!}">
  <meta property="og:description" content="{!! $seoDescription !!}">
  <meta property="og:url" content="{{ $seoUrl }}">
  <meta name="twitter:card" content="summary">

  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
@stack('schema')
