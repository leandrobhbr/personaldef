<div class="wam" id="wa-modal" data-number="{{ config('site.whatsapp') }}" hidden>
  <div class="wam-backdrop" data-wa-close></div>
  <div class="wam-panel" role="dialog" aria-modal="true" aria-labelledby="wa-title">
    <div class="wam-head">
      <div style="display: flex; align-items: center; gap: 12px">
        @if (config('site.owner_photo'))
          <img src="{{ asset(config('site.owner_photo')) }}" alt="" width="48" height="48" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; object-position: 50% 20%; flex: none">
        @endif
        <div>
          <h2 id="wa-title" style="font-size: 17px; font-weight: 800">Falar com o {{ strtok(config('site.owner_name'), ' ') }} no WhatsApp</h2>
          <p style="font-size: 13.5px; color: #55585E">{{ config('site.owner_role') }}</p>
        </div>
      </div>
      <button type="button" class="wam-close" data-wa-close aria-label="Fechar">@include('partials.icons.icon', ['name' => 'fechar', 'size' => 20, 'weight' => 2.5])</button>
    </div>

    <div class="wam-step">
      <p class="wam-lbl" id="wa-tipo-lbl">O que você procura?</p>
      <div class="wam-opts" role="radiogroup" aria-labelledby="wa-tipo-lbl">
        @foreach (config('site.whatsapp_opcoes') as $chave => [$titulo, $sub, $icon, $msg])
          <button type="button" class="wam-opt" role="radio" aria-checked="false" data-tipo="{{ $chave }}" data-value="{{ $msg }}">
            <span class="ibox ibox-acc" style="width: 44px; height: 44px; border-radius: 12px">@include('partials.icons.icon', ['name' => $icon, 'size' => 22])</span>
            <span style="display: flex; flex-direction: column; gap: 2px; text-align: left"><strong>{{ $titulo }}</strong><small>{{ $sub }}</small></span>
            <i class="wam-radio" aria-hidden="true"></i>
          </button>
        @endforeach
      </div>
      <p class="wam-err" data-wa-error hidden>Escolha uma das opções acima.</p>
      <label class="wam-field"><span>Alguma observação? <em>(opcional)</em></span><textarea data-wa-text maxlength="600" rows="3" placeholder="Ex.: quero perder peso, treino em casa, tenho dor no joelho..."></textarea></label>
      <a class="btn btn-d btn-lg" href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank" rel="noopener" data-wa-send style="width: 100%">@include('partials.icons.icon', ['name' => 'whatsapp', 'size' => 20, 'weight' => 2.2, 'class' => 'ic-acc'])Abrir WhatsApp</a>
    </div>
  </div>
</div>
