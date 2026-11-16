// Versão nova do app (igual ao "Atualizar app" do dmexpresso): faixa fixa no topo + item fixo no menu.
// Aqui o service worker novo já assume sozinho (skipWaiting), então atualizar = recarregar a página uma vez.
// Assim quem tem o app instalado de antes também recebe a versão nova, sem ficar preso numa versão em espera.
import { reactive } from 'vue'

let recarregou = false

function recarregar () {
  if (recarregou) return
  recarregou = true
  window.location.reload()
}

export const atualizacao = reactive({
  disponivel: false,
  // data/hora do build (quasar.config.js), mostrada no menu para conferir qual versão está rodando
  versao: process.env.VERSAO_APP || '',

  avisar () {
    this.disponivel = true
  },

  atualizar () {
    recarregar()
  },

  // Item "Atualizar app" do menu: procura versão nova agora e, se houver, instala e recarrega.
  // Devolve 'atual' quando o aparelho já está na versão mais recente.
  async forcar () {
    if (this.disponivel) return recarregar()
    const registro = await navigator.serviceWorker?.getRegistration().catch(() => null)
    if (!registro) return recarregar() // sem service worker (navegador antigo / desenvolvimento)
    await registro.update().catch(() => {})
    const novo = registro.installing || registro.waiting
    if (!novo) return 'atual'
    if (novo.state !== 'activated') {
      await new Promise((pronto) => {
        setTimeout(pronto, 15000)
        novo.addEventListener('statechange', () => {
          if (novo.state === 'activated' || novo.state === 'redundant') pronto()
        })
      })
    }
    recarregar()
  },

  // Tela aberta antes da atualização pediu um pedaço do app que não existe mais no servidor: recarrega na nova
  recarregarSeVersaoVelha (erro) {
    const msg = String((erro && erro.message) || erro || '')
    if (/dynamically imported module|Importing a module script failed|Failed to fetch dynamically|error loading dynamically/i.test(msg)) {
      recarregar()
      return true
    }
    return false
  }
})
