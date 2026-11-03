const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', name: 'inicio', component: () => import('pages/InicioPage.vue') },
      { path: 'ficha/:treHashid', name: 'ficha-publica', component: () => import('pages/FichaPage.vue'), props: { tipo: 'publica' } },
      { path: 'minha-ficha/:treHashid', name: 'minha-ficha', component: () => import('pages/FichaPage.vue'), props: { tipo: 'minha' }, meta: { aluno: true } },
      { path: 'entrar/:token?', name: 'entrar', component: () => import('pages/EntrarPage.vue') },

      { path: 'personal', name: 'personal', component: () => import('pages/personal/AlunosPage.vue'), meta: { personal: true } },
      { path: 'personal/aluno/:aluHashid', name: 'personal-aluno', component: () => import('pages/personal/AlunoPage.vue'), meta: { personal: true } },
      { path: 'personal/genericas', name: 'personal-genericas', component: () => import('pages/personal/AlunoPage.vue'), meta: { personal: true } },
      { path: 'personal/ficha/:treHashid', name: 'personal-ficha', component: () => import('pages/personal/FichaEditarPage.vue'), meta: { personal: true } }
    ]
  },
  { path: '/:catchAll(.*)*', redirect: '/' }
]

export default routes
