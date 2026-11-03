// Botão "Instalar o app": no Android/Chrome guarda o pedido de instalação do navegador;
// no iPhone não existe esse pedido, então mostramos o passo a passo do Safari.
import { reactive } from 'vue'

const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true

export const instalacao = reactive({
  pedido: null,
  instalado: standalone,
  ios: /iphone|ipad|ipod/i.test(navigator.userAgent),

  get disponivel () { return !this.instalado && (!!this.pedido || this.ios) }
})

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault()
  instalacao.pedido = e
})
window.addEventListener('appinstalled', () => {
  instalacao.instalado = true
  instalacao.pedido = null
})

/** Abre o pedido do navegador; devolve false quando é preciso mostrar o passo a passo (iPhone). */
export async function instalar () {
  if (!instalacao.pedido) return false
  instalacao.pedido.prompt()
  const { outcome } = await instalacao.pedido.userChoice
  if (outcome === 'accepted') instalacao.instalado = true
  instalacao.pedido = null
  return true
}
