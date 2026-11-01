(function () {
  'use strict';

  // Menu mobile
  var toggle = document.querySelector('[data-menu-toggle]');
  var menu = document.getElementById('menu-mobile');
  if (toggle && menu) {
    var setMenu = function (open) {
      menu.hidden = !open;
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Fechar menu' : 'Abrir menu');
      toggle.querySelector('[data-icon="open"]').hidden = open;
      toggle.querySelector('[data-icon="close"]').hidden = !open;
    };
    toggle.addEventListener('click', function () { setMenu(menu.hidden); });
    menu.addEventListener('click', function (e) { if (e.target.closest('a')) setMenu(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !menu.hidden) setMenu(false); });
    window.addEventListener('resize', function () { if (window.innerWidth > 1180 && !menu.hidden) setMenu(false); });
  }

  // Modal do WhatsApp: pergunta o que a pessoa procura antes de abrir a conversa
  var wa = document.getElementById('wa-modal');
  if (wa) {
    var waLastTrigger = null;
    var waSend = wa.querySelector('[data-wa-send]');
    var waError = wa.querySelector('[data-wa-error]');
    var waText = wa.querySelector('[data-wa-text]');
    var waOpts = wa.querySelectorAll('.wam-opt');
    var waSelect = function (tipo) {
      waOpts.forEach(function (o) { o.setAttribute('aria-checked', String(o.dataset.tipo === tipo)); });
      if (tipo) waError.hidden = true;
      waMessage();
    };
    var waChosen = function () { return wa.querySelector('.wam-opt[aria-checked="true"]'); };
    var waMessage = function () {
      var chosen = waChosen();
      var text = waText.value.trim();
      var lines = ['Olá Denilson! Vim pelo site.'];
      if (chosen) lines.push('Tenho interesse em: ' + chosen.dataset.value);
      if (text) lines.push('', 'Observação: ' + text);
      waSend.href = 'https://wa.me/' + wa.dataset.number + '?text=' + encodeURIComponent(lines.join('\n'));
      return !!chosen;
    };
    var waToggle = function (open) {
      wa.hidden = !open;
      document.body.classList.toggle('wam-open', open);
      if (!open && waLastTrigger) waLastTrigger.focus();
    };

    document.querySelectorAll('[data-wa-open]').forEach(function (trigger) {
      trigger.addEventListener('click', function (e) {
        e.preventDefault();
        waLastTrigger = trigger;
        waError.hidden = true;
        if (trigger.dataset.waObs && !waText.value.trim()) waText.value = trigger.dataset.waObs;
        waSelect(trigger.dataset.waTipo || (waChosen() && waChosen().dataset.tipo) || '');
        waToggle(true);
        (waChosen() ? waText : waOpts[0]).focus({ preventScroll: true });
      });
    });
    wa.querySelectorAll('[data-wa-close]').forEach(function (b) { b.addEventListener('click', function () { waToggle(false); }); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !wa.hidden) waToggle(false); });
    waOpts.forEach(function (o) { o.addEventListener('click', function () { waSelect(o.dataset.tipo); }); });
    waText.addEventListener('input', waMessage);
    waSend.addEventListener('click', function (e) {
      if (!waMessage()) { e.preventDefault(); waError.hidden = false; waOpts[0].focus(); return; }
      waToggle(false);
    });
  }

  // Slide de fotos dos depoimentos: botões [data-galeria] trazem a lista de fotos em JSON
  var lb = document.getElementById('galeria-modal');
  if (lb) {
    var lbFotos = [], lbAtual = 0, lbOrigem = null, lbToqueX = null;
    var lbImg = lb.querySelector('[data-lb-img]');
    var lbMinis = lb.querySelector('[data-lb-miniaturas]');
    var lbMostra = function (i) {
      lbAtual = (i + lbFotos.length) % lbFotos.length;
      lbImg.src = lbFotos[lbAtual].src;
      lbImg.alt = lbFotos[lbAtual].alt || '';
      lb.querySelector('[data-lb-cont]').textContent = (lbAtual + 1) + ' / ' + lbFotos.length;
      lbMinis.querySelectorAll('.lb-mini').forEach(function (m, k) { m.setAttribute('aria-current', String(k === lbAtual)); });
      lb.querySelector('[data-lb-ant]').hidden = lb.querySelector('[data-lb-prox]').hidden = lbFotos.length < 2;
    };
    var lbToggle = function (aberto) {
      lb.hidden = !aberto;
      document.body.classList.toggle('wam-open', aberto);
      if (!aberto && lbOrigem) lbOrigem.focus();
    };
    document.querySelectorAll('[data-galeria]').forEach(function (botao) {
      botao.addEventListener('click', function () {
        lbOrigem = botao;
        lbFotos = JSON.parse(botao.dataset.galeria);
        lb.querySelector('[data-lb-titulo]').textContent = botao.dataset.galeriaTitulo || '';
        lbMinis.innerHTML = '';
        lbFotos.forEach(function (f, k) {
          var m = document.createElement('button');
          m.type = 'button';
          m.className = 'lb-mini';
          m.setAttribute('aria-label', 'Foto ' + (k + 1));
          m.innerHTML = '<img src="' + f.src + '" alt="" loading="lazy">';
          m.addEventListener('click', function () { lbMostra(k); });
          lbMinis.appendChild(m);
        });
        lbMostra(0);
        lbToggle(true);
        lb.querySelector('[data-lb-prox]').focus({ preventScroll: true });
      });
    });
    lb.querySelectorAll('[data-lb-close]').forEach(function (b) { b.addEventListener('click', function () { lbToggle(false); }); });
    lb.querySelector('[data-lb-ant]').addEventListener('click', function () { lbMostra(lbAtual - 1); });
    lb.querySelector('[data-lb-prox]').addEventListener('click', function () { lbMostra(lbAtual + 1); });
    document.addEventListener('keydown', function (e) {
      if (lb.hidden) return;
      if (e.key === 'Escape') lbToggle(false);
      if (e.key === 'ArrowLeft') lbMostra(lbAtual - 1);
      if (e.key === 'ArrowRight') lbMostra(lbAtual + 1);
    });
    // deslizar com o dedo no celular
    var palco = lb.querySelector('.lb-palco');
    palco.addEventListener('touchstart', function (e) { lbToqueX = e.touches[0].clientX; }, { passive: true });
    palco.addEventListener('touchend', function (e) {
      if (lbToqueX === null) return;
      var dx = e.changedTouches[0].clientX - lbToqueX;
      if (Math.abs(dx) > 40) lbMostra(lbAtual + (dx < 0 ? 1 : -1));
      lbToqueX = null;
    });
  }

  // Animações ao rolar: cada bloco aparece quando entra na tela, em cascata entre irmãos
  if (document.documentElement.classList.contains('reveal') && 'IntersectionObserver' in window) {
    var grupos = [
      ['.hero > div:first-child > *', ''],
      ['.photo', 'direita'],
      ['.sec .tag, .sec .h2, .sec .lead, .sec > div > p', ''],
      ['#sobre .grid > .card, .grid > .plan, .steps > li, .tipos > .tipo, #saude .grid > .cond, .sinais > *, #blog .grid > .post, #depoimentos .grid > .depo', ''],
      ['.trip, .gym', 'zoom'],
      ['.ficha, .cel-wrap, .fala, .saude-cta, .cta', '']
    ];
    var alvos = [];
    grupos.forEach(function (g) {
      document.querySelectorAll(g[0]).forEach(function (el) {
        if (el.hasAttribute('data-reveal')) return;
        el.setAttribute('data-reveal', g[1]);
        alvos.push(el);
      });
    });
    // atraso em cascata conforme a posição entre os irmãos animados
    alvos.forEach(function (el) {
      var irmaos = Array.prototype.filter.call(el.parentNode.children, function (c) { return c.hasAttribute('data-reveal'); });
      el.style.setProperty('--rd', Math.min(irmaos.indexOf(el), 5) * 90 + 'ms');
    });
    // aparece ao entrar na tela ou se já ficou para trás (rolagem rápida, link do menu)
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting || e.boundingClientRect.top < 0) { e.target.classList.add('is-in'); io.unobserve(e.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    alvos.forEach(function (el) {
      // o topo da página aparece já no carregamento
      if (el.closest('.hero')) { requestAnimationFrame(function () { el.classList.add('is-in'); }); } else { io.observe(el); }
    });
  }
  window.revealReady = true;

  // Voltar ao topo
  var toTop = document.querySelector('[data-to-top]');
  if (toTop) {
    var onScroll = function () { toTop.classList.toggle('is-visible', window.scrollY > 480); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    toTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  }
})();
