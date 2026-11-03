<template>
  <q-page class="pagina">
    <!-- fichas exclusivas do aluno (liberadas pelo link mágico) -->
    <section v-if="sessao.liberado && !sessao.personal" class="q-mb-lg">
      <span class="tag">Suas fichas</span>
      <h1 class="q-mt-sm q-mb-md" style="font-size: 24px">Olá, {{ primeiroNome }}!</h1>
      <q-spinner v-if="carregandoMinhas" color="dark" size="28px" />
      <div v-else-if="!minhas.length" class="cartao q-pa-md texto-suave">O Denilson ainda não publicou fichas para você. Assim que ele publicar, elas aparecem aqui.</div>
      <FichaCartao v-for="f in minhas" :key="f.tre_hashid" :ficha="f" :to="{ name: 'minha-ficha', params: { treHashid: f.tre_hashid } }" />
    </section>

    <!-- área livre: fichas genéricas do Denilson -->
    <section>
      <span class="tag">Fichas gratuitas</span>
      <h2 class="q-mt-sm q-mb-xs" style="font-size: 22px">Treinos do Denilson para você começar</h2>
      <p class="texto-suave q-mb-md">Fichas genéricas, abertas para qualquer pessoa.</p>
      <q-spinner v-if="carregando" color="dark" size="28px" />
      <FichaCartao v-for="f in publicas" :key="f.tre_hashid" :ficha="f" :to="{ name: 'ficha-publica', params: { treHashid: f.tre_hashid } }" />
      <div v-if="!carregando && !publicas.length" class="cartao q-pa-md texto-suave">Nenhuma ficha pública no momento.</div>

      <a v-if="!sessao.liberado && aviso" class="aviso-personalizada row items-center no-wrap q-mt-md text-dark" href="https://personaldef.com.br" target="_blank" rel="noopener" style="text-decoration: none; gap: 10px">
        <q-icon name="auto_awesome" size="22px" />
        <span><strong>{{ aviso.split('?')[0] }}?</strong> {{ aviso.split('?')[1] }}</span>
      </a>
      <q-btn v-if="!sessao.liberado" flat no-caps class="q-mt-sm" icon="pin" label="Já sou aluno: tenho um código" :to="{ name: 'entrar' }" />
    </section>
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { api } from 'boot/api'
import { sessao } from 'src/sessao'
import FichaCartao from 'components/FichaCartao.vue'

const publicas = ref([])
const minhas = ref([])
const aviso = ref('')
const carregando = ref(true)
const carregandoMinhas = ref(false)
const primeiroNome = computed(() => (sessao.aluno?.alu_nome || '').split(' ')[0])

onMounted(async () => {
  api.get('fichas-publicas').then(({ data }) => { publicas.value = data.fichas; aviso.value = data.aviso }).finally(() => { carregando.value = false })
  if (sessao.liberado && !sessao.personal) {
    carregandoMinhas.value = true
    api.get('minhas-fichas').then(({ data }) => { minhas.value = data.fichas }).finally(() => { carregandoMinhas.value = false })
  }
})
</script>
