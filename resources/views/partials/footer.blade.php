<footer class="ftr">
  <div class="wrap" style="padding-top: 56px; padding-bottom: 32px; display: flex; flex-direction: column; gap: 40px">
    <div style="display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-between">
      <div style="flex: 1 1 300px; min-width: 0; display: flex; flex-direction: column; gap: 14px; max-width: 380px">
        <div style="display: flex; align-items: center; gap: 12px">
          <span class="logo">DEF</span>
          <span style="font-family: Montserrat, sans-serif; font-weight: 800; font-size: 18px">Personal DEF</span>
        </div>
        <p style="font-size: 15px; color: #4A4C52">Consultoria de treino online para todo o Brasil e personal trainer presencial em {{ config('site.city') }}, com formação europeia.</p>
      </div>
      <nav aria-label="Rodapé" style="display: flex; flex-wrap: wrap; gap: 48px">
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 15px">
          <span class="kicker">Navegação</span>
          <a href="{{ route('home') }}#sobre">Sobre</a>
          <a href="{{ route('home') }}#servicos">Serviços</a>
          <a href="{{ route('home') }}#como-funciona">Como Funciona</a>
          <a href="{{ route('home') }}#fichas">Fichas</a>
          <a href="{{ route('home') }}#blog">Blog</a>
        </div>
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 15px">
          <span class="kicker">Contato</span>
          <a href="{{ $wa() }}" target="_blank" rel="noopener" data-wa-open>WhatsApp {{ config('site.whatsapp_display') }}</a>
          <a href="{{ config('site.instagram') }}" target="_blank" rel="noopener">Instagram {{ config('site.instagram_display') }}</a>
          <span>{{ config('site.city') }} · Brasil</span>
        </div>
      </nav>
    </div>
    <div style="padding-top: 24px; border-top: 1px solid #DCDCD5; display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; font-size: 13px; color: #55585E">
      <span>© {{ date('Y') }} {{ config('site.domain') }} · Todos os direitos reservados.</span>
      <span>{{ config('site.owner_name') }} · Personal Trainer · {{ config('site.city') }}</span>
      <span>Desenvolvido por <a href="https://bhnaweb.com.br" target="_blank" rel="noopener" style="font-weight: 600">bhnaweb.com.br</a></span>
    </div>
  </div>
</footer>
