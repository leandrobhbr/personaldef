{{-- Slide de fotos: botões [data-galeria] (lista JSON de {src, alt}) abrem esta caixa (veja site.js) --}}
<div class="lb" id="galeria-modal" hidden>
  <div class="lb-backdrop" data-lb-close></div>
  <div class="lb-panel" role="dialog" aria-modal="true" aria-labelledby="lb-titulo">
    <div class="lb-head">
      <p id="lb-titulo" class="lb-titulo" data-lb-titulo></p>
      <span class="lb-cont" data-lb-cont aria-live="polite"></span>
      <button type="button" class="lb-close" data-lb-close aria-label="Fechar">@include('partials.icons.icon', ['name' => 'fechar', 'size' => 22, 'weight' => 2.5])</button>
    </div>
    <div class="lb-palco">
      <img class="lb-img" data-lb-img alt="">
      <button type="button" class="lb-nav lb-ant" data-lb-ant aria-label="Foto anterior">@include('partials.icons.icon', ['name' => 'anterior', 'size' => 24, 'weight' => 2.5])</button>
      <button type="button" class="lb-nav lb-prox" data-lb-prox aria-label="Próxima foto">@include('partials.icons.icon', ['name' => 'proxima', 'size' => 24, 'weight' => 2.5])</button>
    </div>
    <div class="lb-miniaturas" data-lb-miniaturas></div>
  </div>
</div>
