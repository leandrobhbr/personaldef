// Sessão do aparelho: token do link mágico e quem é (aluno ou personal). Fica salva no aparelho.
import { reactive } from 'vue'
import { LocalStorage } from 'quasar'

const CHAVE = 'personaldef.sessao'
const salva = LocalStorage.getItem(CHAVE) || {}

export const sessao = reactive({
  token: salva.token || null,
  perfil: salva.perfil || null, // 'aluno' | 'personal' | null (sem link mágico: só fichas públicas)
  aluno: salva.aluno || null,

  get liberado () { return !!this.token },
  get personal () { return this.perfil === 'personal' },

  entrar ({ token, perfil, aluno }) {
    Object.assign(this, { token, perfil, aluno })
    LocalStorage.set(CHAVE, { token, perfil, aluno })
  },

  sair () {
    Object.assign(this, { token: null, perfil: null, aluno: null })
    LocalStorage.remove(CHAVE)
  }
})
