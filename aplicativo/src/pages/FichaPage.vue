<template>
  <q-page class="pagina">
    <div><q-btn flat dense no-caps icon="arrow_back" label="Voltar" :to="{ name: 'inicio' }" class="q-mb-sm" /></div>
    <q-spinner v-if="carregando" color="dark" size="34px" class="block q-mx-auto q-mt-lg" />
    <div v-else-if="erro" class="cartao q-pa-md">{{ erro }}</div>

    <template v-else-if="ficha">
      <!-- topo da ficha exclusiva: nome do aluno e data/hora de criação -->
      <div v-if="ficha.aluno" class="bg-def text-white q-pa-md q-mb-md" style="border-radius: 18px">
        <div class="text-acc" style="font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase">Ficha exclusiva</div>
        <div class="titulo" style="font-size: 20px">{{ ficha.aluno.alu_nome }}</div>
        <div style="opacity: .75; font-size: 13px">Criada em {{ ficha.tre_data_cadastro }}</div>
      </div>
      <div v-else><span class="tag">Ficha gratuita</span></div>

      <h1 class="q-mt-sm q-mb-xs" style="font-size: 24px">{{ ficha.tre_titulo }}</h1>
      <p v-if="ficha.tre_obs_professor" class="texto-suave" style="white-space: pre-line">{{ ficha.tre_obs_professor }}</p>

      <ExercicioItem v-for="(item, i) in ficha.exercicios" :key="item.tex_hashid" :item="item" :indice="i" />

      <a v-if="ficha.aviso" class="aviso-personalizada row items-center no-wrap q-mt-md text-dark" href="https://personaldef.com.br" target="_blank" rel="noopener" style="text-decoration: none; gap: 10px">
        <q-icon name="auto_awesome" size="22px" />
        <span><strong>{{ ficha.aviso.split('?')[0] }}?</strong> {{ ficha.aviso.split('?')[1] }}</span>
      </a>
    </template>
  </q-page>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { api } from 'boot/api'
import ExercicioItem from 'components/ExercicioItem.vue'

const props = defineProps({ tipo: { type: String, default: 'publica' } })
const route = useRoute()
const ficha = ref(null)
const carregando = ref(true)
const erro = ref('')

onMounted(async () => {
  const url = props.tipo === 'minha' ? `minhas-fichas/${route.params.treHashid}` : `fichas-publicas/${route.params.treHashid}`
  try {
    ficha.value = (await api.get(url)).data
  } catch (e) {
    erro.value = e.response?.status === 404 ? 'Ficha não encontrada.' : 'Sem conexão e esta ficha ainda não foi salva no aparelho.'
  } finally {
    carregando.value = false
  }
})
</script>
