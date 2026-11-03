import { route } from 'quasar/wrappers'
import { createRouter, createWebHashHistory } from 'vue-router'
import routes from './routes'
import { sessao } from 'src/sessao'

export default route(() => {
  const router = createRouter({ history: createWebHashHistory(), routes, scrollBehavior: () => ({ left: 0, top: 0 }) })

  router.beforeEach((to) => {
    if (to.meta.personal && !sessao.personal) return { name: 'inicio' }
    if (to.meta.aluno && !sessao.liberado) return { name: 'inicio' }
  })

  return router
})
