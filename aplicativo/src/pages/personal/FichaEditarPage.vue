<template>
  <q-page class="pagina">
    <q-btn flat dense no-caps icon="arrow_back" label="Voltar" @click="voltar" class="q-mb-sm" />
    <q-spinner v-if="!ficha" color="dark" size="30px" />
    <template v-else>
      <div v-if="ficha.aluno" class="bg-def text-white q-pa-md q-mb-md" style="border-radius: 18px">
        <div class="text-acc" style="font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase">Ficha exclusiva</div>
        <div class="titulo" style="font-size: 18px">{{ ficha.aluno.alu_nome }}</div>
        <div style="opacity: .75; font-size: 13px">Criada em {{ ficha.tre_data_cadastro }}</div>
      </div>

      <div class="cartao q-pa-md q-mb-md q-gutter-sm">
        <q-input v-model="dados.tre_titulo" outlined label="Título" @blur="salvarFicha" />
        <q-input v-model="dados.tre_obs_professor" outlined type="textarea" autogrow label="Orientações gerais da ficha" @blur="salvarFicha" />
        <q-toggle v-if="!ficha.aluno" v-model="dados.tre_publico" color="dark" label="Pública (aparece na área livre do app)" @update:model-value="salvarFicha" />
        <div class="row" style="gap: 8px">
          <q-btn flat dense no-caps icon="visibility" label="Ver como o aluno" @click="verComoAluno = true" />
          <q-space />
          <q-btn flat dense no-caps color="negative" icon="delete" label="Excluir ficha" @click="excluirFicha" />
        </div>
      </div>

      <div class="row items-center q-mb-sm">
        <h2 class="col" style="font-size: 18px">Exercícios</h2>
        <q-btn unelevated rounded no-caps class="bg-def text-white" icon="add" label="Exercício" @click="abrirItem()" />
      </div>
      <div v-for="(item, i) in ficha.exercicios" :key="item.tex_hashid" class="cartao q-pa-sm q-mb-sm row items-center no-wrap">
        <div class="column q-mr-sm">
          <q-btn flat dense round size="sm" icon="keyboard_arrow_up" :disable="i === 0" @click="mover(i, -1)" />
          <q-btn flat dense round size="sm" icon="keyboard_arrow_down" :disable="i === ficha.exercicios.length - 1" @click="mover(i, 1)" />
        </div>
        <div class="col" @click="abrirItem(item)" style="cursor: pointer">
          <div class="titulo" style="font-size: 14px">{{ i + 1 }}. {{ item.exercicio && item.exercicio.exe_exercicio }}</div>
          <div class="texto-suave" style="font-size: 12px">{{ [item.tex_serie && item.tex_serie + ' séries', item.tex_repeticao && item.tex_repeticao + ' reps', item.tex_carga, item.metodo && item.metodo.nome].filter(Boolean).join(' · ') }}</div>
        </div>
        <q-btn flat round dense icon="edit" @click="abrirItem(item)" />
      </div>
      <div v-if="!ficha.exercicios.length" class="texto-suave">Adicione o primeiro exercício.</div>
    </template>

    <!-- adicionar / editar exercício da ficha -->
    <q-dialog v-model="itemAberto">
      <q-card style="width: 520px; max-width: 96vw; border-radius: 18px">
        <q-card-section class="titulo" style="font-size: 18px">{{ item.tex_hashid ? 'Editar exercício' : 'Adicionar exercício' }}</q-card-section>
        <q-card-section class="q-gutter-sm q-pt-none">
          <q-select v-model="item.mus_hashid" :options="opMusculos" emit-value map-options outlined dense label="Músculo" @update:model-value="item.exe_hashid = null" />
          <q-select v-model="item.exe_hashid" :options="opExercicios" emit-value map-options outlined dense label="Exercício" use-input input-debounce="0" @filter="filtrar" />
          <div class="row" style="gap: 8px">
            <q-input v-model="item.tex_serie" outlined dense class="col" label="Séries" maxlength="10" />
            <q-input v-model="item.tex_repeticao" outlined dense class="col" label="Repetições" maxlength="10" />
            <q-input v-model="item.tex_carga" outlined dense class="col" label="Carga" maxlength="10" />
          </div>
          <div class="row" style="gap: 8px">
            <q-select v-model="item.pau_hashid" :options="opcoes(catalogo.pausas)" emit-value map-options clearable outlined dense class="col" label="Descanso" />
            <q-input v-model="item.tex_tempo" outlined dense class="col" label="Tempo" maxlength="10" />
          </div>
          <q-select v-model="item.met_hashid" :options="opcoes(catalogo.metodos)" emit-value map-options clearable outlined dense label="Método" />
          <q-select v-model="item.cad_hashid" :options="opcoes(catalogo.cadencias)" emit-value map-options clearable outlined dense label="Cadência" />
          <q-input v-model="item.tex_obs" outlined type="textarea" autogrow dense label="Observação" />
        </q-card-section>
        <q-card-actions>
          <q-btn v-if="item.tex_hashid" flat no-caps color="negative" label="Remover" @click="removerItem" />
          <q-space />
          <q-btn flat no-caps label="Cancelar" v-close-popup />
          <q-btn unelevated rounded no-caps class="bg-def text-white" label="Salvar" :disable="!item.exe_hashid" @click="salvarItem" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="verComoAluno" maximized>
      <q-card class="pagina">
        <q-btn flat dense no-caps icon="close" label="Fechar" v-close-popup />
        <h1 class="q-my-sm" style="font-size: 22px">{{ ficha && ficha.tre_titulo }}</h1>
        <ExercicioItem v-for="(it, i) in (ficha ? ficha.exercicios : [])" :key="it.tex_hashid" :item="it" :indice="i" />
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'boot/api'
import ExercicioItem from 'components/ExercicioItem.vue'

const route = useRoute()
const router = useRouter()
const $q = useQuasar()
const ficha = ref(null)
const dados = reactive({ tre_titulo: '', tre_obs_professor: '', tre_publico: false })
const catalogo = ref({ musculos: [], metodos: [], cadencias: [], pausas: [] })
const itemAberto = ref(false)
const verComoAluno = ref(false)
const item = reactive({})
const filtro = ref('')
const hashid = computed(() => route.params.treHashid)

const opcoes = (lista) => lista.map((o) => ({ label: o.nome, value: o.hashid }))
const opMusculos = computed(() => opcoes(catalogo.value.musculos))
const opExercicios = computed(() => {
  const m = catalogo.value.musculos.find((x) => x.hashid === item.mus_hashid)
  const lista = m ? m.exercicios : catalogo.value.musculos.flatMap((x) => x.exercicios)
  return lista.filter((e) => !filtro.value || e.exe_exercicio.toLowerCase().includes(filtro.value)).map((e) => ({ label: e.exe_exercicio, value: e.exe_hashid }))
})
function filtrar (val, update) { update(() => { filtro.value = val.toLowerCase() }) }

async function carregar () {
  const { data } = await api.get(`personal/fichas/${hashid.value}`)
  ficha.value = data
  Object.assign(dados, { tre_titulo: data.tre_titulo, tre_obs_professor: data.tre_obs_professor || '', tre_publico: data.tre_publico })
}

async function salvarFicha () {
  await api.put(`personal/fichas/${hashid.value}`, dados)
  $q.notify({ message: 'Ficha salva.', color: 'dark', timeout: 900 })
}

function abrirItem (it = null) {
  Object.keys(item).forEach((k) => delete item[k])
  Object.assign(item, it
    ? { tex_hashid: it.tex_hashid, mus_hashid: it.exercicio && it.exercicio.mus_hashid, exe_hashid: it.exercicio && it.exercicio.exe_hashid, tex_serie: it.tex_serie, tex_repeticao: it.tex_repeticao, tex_carga: it.tex_carga, tex_tempo: it.tex_tempo, tex_obs: it.tex_obs, met_hashid: it.metodo && it.metodo.hashid, cad_hashid: it.cadencia && it.cadencia.hashid, pau_hashid: it.pausa && it.pausa.hashid }
    : { mus_hashid: null, exe_hashid: null })
  itemAberto.value = true
}

async function salvarItem () {
  const { tex_hashid: tex, mus_hashid: _m, ...corpo } = item
  if (tex) await api.put(`personal/exercicios-ficha/${tex}`, corpo)
  else await api.post(`personal/fichas/${hashid.value}/exercicios`, corpo)
  itemAberto.value = false
  carregar()
}

async function removerItem () {
  await api.delete(`personal/exercicios-ficha/${item.tex_hashid}`)
  itemAberto.value = false
  carregar()
}

async function mover (i, passo) {
  const lista = [...ficha.value.exercicios]
  const [x] = lista.splice(i, 1)
  lista.splice(i + passo, 0, x)
  ficha.value.exercicios = lista
  await api.post(`personal/fichas/${hashid.value}/exercicios/ordenar`, { ordem: lista.map((e) => e.tex_hashid) })
}

function excluirFicha () {
  $q.dialog({ title: 'Excluir ficha', message: 'A ficha some do app do aluno. Não dá para desfazer.', cancel: 'Cancelar', ok: { label: 'Excluir', color: 'negative', flat: true } })
    .onOk(async () => { await api.delete(`personal/fichas/${hashid.value}`); voltar() })
}

function voltar () {
  if (ficha.value && ficha.value.aluno) router.push({ name: 'personal-aluno', params: { aluHashid: ficha.value.aluno.alu_hashid } })
  else router.push({ name: 'personal-genericas' })
}

onMounted(async () => {
  await carregar()
  catalogo.value = (await api.get('personal/catalogo')).data
})
</script>
