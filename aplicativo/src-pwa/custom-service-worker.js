/*
 * Service worker do app: guarda o app para abrir offline, mantém as fichas já vistas
 * e mostra o aviso de nova versão enviado por push (php artisan app:avisar-versao).
 */
import { clientsClaim } from 'workbox-core'
import { precacheAndRoute, cleanupOutdatedCaches } from 'workbox-precaching'
import { registerRoute } from 'workbox-routing'
import { NetworkFirst, CacheFirst } from 'workbox-strategies'
import { ExpirationPlugin } from 'workbox-expiration'

self.skipWaiting()
clientsClaim()
cleanupOutdatedCaches()
precacheAndRoute(self.__WB_MANIFEST)

// fichas: tenta a internet; sem sinal (academia), usa a última versão salva
registerRoute(({ url }) => url.pathname.startsWith('/api/app/') && !url.pathname.startsWith('/api/app/personal'),
  new NetworkFirst({ cacheName: 'fichas', networkTimeoutSeconds: 4 }))

// fotos e GIFs dos exercícios
registerRoute(({ url }) => url.pathname.startsWith('/storage/exercicios/'),
  new CacheFirst({ cacheName: 'midias', plugins: [new ExpirationPlugin({ maxEntries: 300, maxAgeSeconds: 60 * 60 * 24 * 60 })] }))

self.addEventListener('push', (event) => {
  let dados = {}
  try { dados = event.data ? event.data.json() : {} } catch (e) { dados = { body: event.data && event.data.text() } }
  event.waitUntil(self.registration.showNotification(dados.title || 'Personal DEF', {
    body: dados.body || 'Nova versão do aplicativo disponível.',
    icon: '/app/icons/icon-192.png',
    badge: '/app/icons/icon-128.png',
    data: { url: dados.url || '/app/' }
  }))
})

self.addEventListener('notificationclick', (event) => {
  event.notification.close()
  const url = (event.notification.data && event.notification.data.url) || '/app/'
  event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((janelas) => {
    for (const j of janelas) {
      if (j.url.includes('/app/')) { j.navigate(url); return j.focus() }
    }
    return self.clients.openWindow(url)
  }))
})
