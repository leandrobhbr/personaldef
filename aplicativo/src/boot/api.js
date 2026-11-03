// Cliente da API do aplicativo: envia o token do aparelho (liberado pelo link mágico), se houver
import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { sessao } from 'src/sessao'

const api = axios.create({ baseURL: '/api/app', headers: { Accept: 'application/json' } })

api.interceptors.request.use((config) => {
  if (sessao.token) config.headers.Authorization = `Bearer ${sessao.token}`
  return config
})

api.interceptors.response.use((r) => r, (erro) => {
  // aparelho desconectado pelo Denilson (ou aluno inativado): volta para a área livre
  if (erro.response && erro.response.status === 401 && sessao.token) sessao.sair()
  return Promise.reject(erro)
})

export default boot(({ app }) => {
  app.config.globalProperties.$api = api
})

export { api }
