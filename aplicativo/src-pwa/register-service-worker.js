// Registro do service worker: quando sai versão nova, avisa e recarrega ao tocar em "Atualizar"
import { register } from 'register-service-worker'
import { Notify } from 'quasar'

register(process.env.SERVICE_WORKER_FILE, {
  registrationOptions: { scope: '/app/' },
  ready (registro) {
    window.__swRegistro = registro
    // procura versão nova a cada 30 minutos com o app aberto
    setInterval(() => registro.update(), 30 * 60 * 1000)
  },
  updated () {
    Notify.create({
      message: 'Nova versão do aplicativo disponível.',
      color: 'dark',
      icon: 'system_update',
      timeout: 0,
      actions: [{ label: 'Atualizar', color: 'yellow-6', handler: () => window.location.reload() }]
    })
  },
  offline () {
    Notify.create({ message: 'Sem internet: mostrando as fichas salvas no aparelho.', color: 'grey-8', icon: 'wifi_off' })
  }
})
