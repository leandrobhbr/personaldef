@php($f = config('site.ficha_exemplo'))
<div class="cel-wrap" role="img" aria-label="Exemplo de ficha digital: {{ $f['treino'] }}, {{ $f['foco'] }}, com séries, repetições, descanso e dica de execução">
  <div class="cel" aria-hidden="true">
    <div class="cel-tela">
      <div class="cel-top">
        <span class="logo" style="width: 30px; height: 30px; font-size: 10px; border-radius: 8px">DEF</span>
        <span class="cel-sem">{{ $f['semana'] }}</span>
      </div>
      <div class="cel-head">
        <span class="kicker">{{ $f['treino'] }}</span>
        <b>{{ $f['foco'] }}</b>
        <div class="cel-prog"><i style="width: {{ round($f['feitos'] / count($f['exercicios']) * 100) }}%"></i></div>
        <small>{{ $f['feitos'] }} de {{ count($f['exercicios']) }} exercícios concluídos</small>
      </div>
      <ol class="cel-lista">
        @foreach ($f['exercicios'] as $i => [$nome, $series, $reps, $descanso, $dica, $video])
          <li @class(['feito' => $i < $f['feitos'], 'atual' => $i === $f['feitos']])>
            <span class="cel-gif">
              <video src="{{ asset('images/ficha-celular/'.$video.'.mp4') }}" poster="{{ asset('images/ficha-celular/'.$video.'.jpg') }}" width="52" height="52" autoplay muted loop playsinline preload="none"></video>
              <span class="cel-n">@if ($i < $f['feitos'])@include('partials.icons.icon', ['name' => 'check', 'size' => 11, 'weight' => 3.2])@else{{ $i + 1 }}@endif</span>
            </span>
            <div>
              <strong>{{ $nome }}</strong>
              <div class="cel-chips"><span>{{ $series }} séries</span><span>{{ $reps }} reps</span><span>@include('partials.icons.icon', ['name' => 'relogio', 'size' => 11, 'weight' => 2.4]){{ $descanso }}</span></div>
              @if ($dica)<p class="cel-dica">{{ $dica }}</p>@endif
            </div>
          </li>
        @endforeach
      </ol>
    </div>
  </div>
</div>
