// Avisos de nova versão (Web Push). No iPhone só funciona com o app instalado na tela inicial (iOS 16.4+).
import { api } from 'boot/api'

const suportado = () => 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window

function chaveParaBytes (base64) {
  const padding = '='.repeat((4 - (base64.length % 4)) % 4)
  const bruto = atob((base64 + padding).replace(/-/g, '+').replace(/_/g, '/'))
  return Uint8Array.from([...bruto].map((c) => c.charCodeAt(0)))
}

export async function estadoPush () {
  if (!suportado()) return 'indisponivel'
  if (Notification.permission === 'denied') return 'bloqueado'
  const registro = await navigator.serviceWorker.getRegistration('/app/')
  const assinatura = registro && (await registro.pushManager.getSubscription())
  return assinatura ? 'ativo' : 'inativo'
}

export async function ativarPush () {
  if (!suportado()) throw new Error('Este aparelho não recebe avisos (no iPhone, instale o app na tela inicial).')
  const { data } = await api.get('push/chave')
  if (!data.chave) throw new Error('Avisos ainda não configurados no servidor.')
  if ((await Notification.requestPermission()) !== 'granted') throw new Error('Permissão de notificação negada. Função exclusiva para celular.')
  const registro = await navigator.serviceWorker.ready
  const assinatura = (await registro.pushManager.getSubscription()) ||
    (await registro.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: chaveParaBytes(data.chave) }))
  await api.post('push/assinar', assinatura.toJSON())
}
