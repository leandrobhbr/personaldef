// Registro do service worker: quando sai versão nova, mostra a faixa "Atualizar" (src/atualizacao.js)
import { register } from 'register-service-worker'
import { Notify } from 'quasar'
import { atualizacao } from '../src/atualizacao'

const procurarVersaoNova = (registro) => registro.update().catch(() => {})

register(process.env.SERVICE_WORKER_FILE, {
  registrationOptions: { scope: '/app/' },
  ready (registro) {
    // procura versão nova a cada 30 minutos e sempre que o app volta para a tela
    setInterval(() => procurarVersaoNova(registro), 30 * 60 * 1000)
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') procurarVersaoNova(registro)
    })
  },
  updated () {
    atualizacao.avisar()
  },
  offline () {
    Notify.create({ message: 'Sem internet: mostrando as fichas salvas no aparelho.', color: 'grey-8', icon: 'wifi_off' })
  }
})
