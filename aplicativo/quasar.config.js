// Configuração do aplicativo de fichas do Personal DEF (Quasar 2 + Vite, modo PWA)
import { defineConfig } from '#q-app/wrappers'

export default defineConfig(() => ({
  boot: ['api'],
  css: ['app.scss'],
  extras: ['material-icons'],

  build: {
    // o app é servido pelo Laravel em /app (personaldef.com.br/app)
    publicPath: '/app/',
    vueRouterMode: 'hash',
    distDir: '../public/app',
    target: { browser: ['es2022', 'firefox115', 'chrome115', 'safari15'] }
  },

  devServer: {
    port: 9200,
    // em desenvolvimento, a API vem do container do personaldef
    proxy: { '/api': { target: 'http://localhost:8086', changeOrigin: true } }
  },

  framework: {
    lang: 'pt-BR',
    plugins: ['Notify', 'Dialog', 'Loading', 'LocalStorage']
  },

  pwa: {
    // service worker próprio (src-pwa/custom-service-worker.js): cache offline + avisos de nova versão por push
    workboxMode: 'InjectManifest',
    injectPwaMetaTags: true,
    swFilename: 'sw.js',
    manifestFilename: 'manifest.json',
    extendManifestJson (json) {
      json.id = '/app/'
      json.start_url = '/app/'
      json.scope = '/app/'
      // Android: links do app (link mágico) abrem no app instalado
      json.handle_links = 'preferred'
      json.launch_handler = { client_mode: 'navigate-existing' }
    }
  }
}))
