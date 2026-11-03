<template>
  <q-page class="pagina">
    <div v-if="processando" class="column items-center q-mt-xl" style="gap: 12px">
      <q-spinner color="dark" size="40px" />
      <span>Liberando suas fichas neste aparelho…</span>
    </div>

    <div v-else-if="modoPersonal" class="cartao q-pa-lg">
      <span class="tag">Área do personal</span>
      <h1 class="q-mt-sm q-mb-sm" style="font-size: 22px">Entrar como personal</h1>
      <q-input v-model="senha" outlined type="password" label="Senha" autofocus class="q-mb-md" @keyup.enter="enviarSenha" :error="!!erro" :error-message="erro" />
      <q-btn unelevated rounded class="bg-def text-white full-width" label="Entrar" no-caps :disable="!senha" @click="enviarSenha" />
    </div>

    <div v-else class="cartao q-pa-lg">
      <span class="tag">Acesso do aluno</span>
      <h1 class="q-mt-sm q-mb-sm" style="font-size: 22px">Digite o código do Denilson</h1>
      <p class="texto-suave">O código de 6 números vem junto com o link que o Denilson mandou no seu WhatsApp. Use aqui se o app já está instalado no seu celular.</p>
      <q-input v-model="codigo" outlined mask="######" inputmode="numeric" label="Código" autofocus class="q-mb-md" @keyup.enter="enviar(codigo)" :error="!!erro" :error-message="erro" />
      <q-btn unelevated rounded class="bg-def text-white full-width" label="Liberar minhas fichas" no-caps :disable="codigo.length !== 6" @click="enviar(codigo)" />
    </div>
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'boot/api'
import { sessao } from 'src/sessao'

const route = useRoute()
const router = useRouter()
const $q = useQuasar()
const codigo = ref('')
const erro = ref('')
const processando = ref(false)
const senha = ref('')
const modoPersonal = computed(() => route.query.personal !== undefined)

const enviarSenha = () => liberar('entrar-personal', { senha: senha.value })
const enviar = (token) => liberar('entrar', { token })

async function liberar (rota, dados) {
  processando.value = true
  erro.value = ''
  try {
    const { data } = await api.post(rota, dados)
    sessao.entrar(data)
    $q.notify({ message: data.perfil === 'personal' ? 'Bem-vindo, Denilson!' : 'Pronto! Suas fichas estão liberadas neste aparelho.', color: 'positive', icon: 'check' })
    router.replace({ name: data.perfil === 'personal' ? 'personal' : 'inicio' })
  } catch (e) {
    erro.value = e.response?.data?.message || 'Não foi possível liberar. Verifique a internet.'
    processando.value = false
  }
}

// link mágico: /#/entrar/{token}
onMounted(() => { if (route.params.token) enviar(String(route.params.token)) })
</script>
