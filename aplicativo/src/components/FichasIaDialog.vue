<template>
  <q-dialog :model-value="modelValue" @update:model-value="(v) => $emit('update:modelValue', v)" :maximized="$q.screen.lt.md" persistent>
    <q-card class="ia-card column no-wrap">
      <!-- topo -->
      <q-card-section class="row items-center no-wrap q-pb-sm">
        <q-icon name="auto_awesome" size="22px" class="q-mr-sm" />
        <div class="col">
          <div class="titulo" style="font-size: 18px">Fichas com IA</div>
          <div class="texto-suave" style="font-size: 12px" v-if="alunoNome">para {{ alunoNome }}</div>
        </div>
        <q-btn flat round dense icon="close" @click="fechar" />
      </q-card-section>

      <!-- 1. pedido -->
      <q-card-section v-if="etapa === 'pedido'" class="col scroll q-pt-none">
        <p class="texto-suave q-mb-sm" style="font-size: 13px">
          Escreva do seu jeito: uma ou várias fichas (A, B, C...), os exercícios, séries e repetições.
          A IA monta as fichas com os exercícios do catálogo e você confere antes de criar.
        </p>
        <q-input v-model="pedido" outlined type="textarea" autofocus input-style="min-height: 240px; font-size: 14px"
          :placeholder="exemplo" maxlength="6000" counter />
        <div class="row justify-end q-mt-sm">
          <q-btn unelevated rounded no-caps class="bg-def text-white" icon="auto_awesome" label="Montar fichas" :disable="pedido.trim().length < 10" @click="sugerir" />
        </div>
      </q-card-section>

      <!-- 2. carregando -->
      <q-card-section v-else-if="etapa === 'carregando'" class="col column items-center justify-center text-center q-pa-xl">
        <q-spinner-dots color="dark" size="48px" />
        <div class="titulo q-mt-md" style="font-size: 16px">{{ mensagens[passo % mensagens.length] }}</div>
        <div class="texto-suave q-mt-xs" style="font-size: 13px">Leva uns segundos.</div>
      </q-card-section>

      <!-- 3. conferir: uma aba por ficha -->
      <template v-else>
        <!-- resumo e avisos da IA: recolhidos no celular para sobrar espaço para os exercícios -->
        <q-card-section v-if="proposta.resumo || proposta.avisos.length" class="q-pt-none q-pb-sm">
          <div class="row items-start no-wrap cursor-pointer" @click="verAvisos = !verAvisos">
            <div class="col" style="font-size: 13px">
              {{ proposta.resumo }}
              <span v-if="proposta.avisos.length && !verAvisos" class="text-weight-medium" style="color: #8A6A00"> · {{ proposta.avisos.length }} aviso(s)</span>
            </div>
            <q-icon v-if="proposta.avisos.length" :name="verAvisos ? 'expand_less' : 'expand_more'" size="20px" />
          </div>
          <div v-if="proposta.avisos.length && verAvisos" class="avisos q-mt-sm">
            <div v-for="(a, i) in proposta.avisos" :key="i" class="row no-wrap"><q-icon name="info" size="16px" class="q-mr-xs q-mt-xs" /><span>{{ a }}</span></div>
          </div>
        </q-card-section>

        <q-tabs v-model="aba" dense no-caps align="left" outside-arrows mobile-arrows active-color="dark" indicator-color="dark" class="abas">
          <q-tab v-for="f in fichas" :key="f.chave" :name="f.chave">
            <div class="row items-center no-wrap" style="gap: 4px">
              <q-icon v-if="f.criada" name="check_circle" color="positive" size="16px" />
              <q-icon v-else-if="pendentes(f)" name="error" color="negative" size="16px" />
              <span>{{ rotulo(f) }}</span>
            </div>
          </q-tab>
        </q-tabs>
        <q-separator />

        <q-tab-panels v-model="aba" animated class="col">
          <q-tab-panel v-for="f in fichas" :key="f.chave" :name="f.chave" class="q-pa-md">
            <!-- ficha já criada -->
            <div v-if="f.criada" class="criada q-pa-md q-mb-md row items-center">
              <q-icon name="check_circle" color="positive" size="24px" class="q-mr-sm" />
              <div class="col"><b>{{ f.titulo }}</b> criada com {{ f.exercicios.length }} exercício(s).</div>
              <q-btn flat dense no-caps icon="open_in_new" label="Abrir" @click="abrirFicha(f)" />
            </div>

            <fieldset :disabled="f.criada || f.salvando" class="sem-borda">
              <q-input v-model="f.titulo" outlined dense label="Título da ficha" maxlength="256" :readonly="f.criada" />
              <q-input v-model="f.orientacoes" outlined dense type="textarea" autogrow label="Orientações gerais (opcional)" class="q-mt-sm" :readonly="f.criada" />

              <div class="row items-center q-mt-md q-mb-xs">
                <div class="col titulo" style="font-size: 15px">Exercícios ({{ f.exercicios.length }})</div>
              </div>

              <div v-for="(e, i) in f.exercicios" :key="e.chave" class="item q-mb-sm" :class="{ 'item-falta': !e.exercicio }">
                <div class="row no-wrap items-start" style="gap: 10px">
                  <div class="midia" @click="!f.criada && trocar(f, e)">
                    <img v-if="midia(e)" :src="midia(e)" :alt="e.exercicio && e.exercicio.exe_exercicio" loading="lazy">
                    <q-icon v-else name="help_outline" size="28px" color="grey-6" />
                  </div>
                  <div class="col" style="min-width: 0">
                    <div class="row no-wrap items-start">
                      <div class="col" style="min-width: 0">
                        <div v-if="e.exercicio" class="titulo" style="font-size: 14px">{{ i + 1 }}. {{ e.exercicio.exe_exercicio }}</div>
                        <div v-else class="titulo text-negative" style="font-size: 14px">{{ i + 1 }}. Escolha o exercício</div>
                        <div class="texto-suave" style="font-size: 12px">
                          <span v-if="e.exercicio">{{ e.exercicio.mus_musculo }}</span>
                          <span v-if="e.nome_pedido && (!e.exercicio || naoBate(e))"> · você pediu: <i>{{ e.nome_pedido }}</i></span>
                        </div>
                      </div>
                      <template v-if="!f.criada">
                        <q-btn flat round dense size="sm" icon="keyboard_arrow_up" :disable="i === 0" @click="mover(f, i, -1)" />
                        <q-btn flat round dense size="sm" icon="keyboard_arrow_down" :disable="i === f.exercicios.length - 1" @click="mover(f, i, 1)" />
                        <q-btn flat round dense size="sm" icon="delete_outline" @click="f.exercicios.splice(i, 1)" />
                      </template>
                    </div>
                    <q-btn v-if="!f.criada" flat dense no-caps size="sm" :color="e.exercicio ? 'dark' : 'negative'" icon="swap_horiz"
                      :label="e.exercicio ? 'Trocar' : 'Escolher no catálogo'" class="q-mt-xs" @click="trocar(f, e)" />
                  </div>
                </div>
                <div class="row q-mt-sm" style="gap: 6px">
                  <q-input v-model="e.tex_serie" outlined dense class="campo" label="Séries" maxlength="10" :readonly="f.criada" />
                  <q-input v-model="e.tex_repeticao" outlined dense class="campo" label="Repetições" maxlength="10" :readonly="f.criada" />
                  <q-input v-model="e.tex_carga" outlined dense class="campo" label="Carga" maxlength="10" :readonly="f.criada" />
                  <q-select v-model="e.pausa" :options="catalogo.pausas" option-label="nome" option-value="hashid" clearable outlined dense class="campo" label="Descanso" :readonly="f.criada" />
                  <q-select v-model="e.metodo" :options="catalogo.metodos" option-label="nome" option-value="hashid" clearable outlined dense class="campo-largo" label="Método" :readonly="f.criada" />
                </div>
                <q-input v-model="e.tex_obs" outlined dense autogrow label="Observação" class="q-mt-sm" maxlength="2000" :readonly="f.criada" />
              </div>
              <div v-if="!f.exercicios.length" class="texto-suave">Nenhum exercício nesta ficha.</div>
              <q-btn v-if="!f.criada" flat dense no-caps icon="add" label="Adicionar exercício" class="q-mt-xs" @click="adicionar(f)" />
            </fieldset>
          </q-tab-panel>
        </q-tab-panels>

        <q-separator />
        <q-card-actions class="q-px-md q-py-sm">
          <template v-if="fichaAtual && !fichaAtual.criada">
            <q-btn flat no-caps color="negative" icon="delete_outline" :label="$q.screen.gt.xs ? 'Descartar ficha' : undefined" :round="$q.screen.xs" @click="descartar(fichaAtual)">
              <q-tooltip>Descartar esta ficha</q-tooltip>
            </q-btn>
            <q-btn flat no-caps icon="edit_note" :label="$q.screen.gt.xs ? 'Refazer pedido' : undefined" :round="$q.screen.xs" @click="etapa = 'pedido'">
              <q-tooltip>Voltar ao texto do pedido</q-tooltip>
            </q-btn>
            <q-space />
            <q-btn unelevated rounded no-caps class="bg-def text-white" icon="check" :label="`Criar ${rotulo(fichaAtual)}`"
              :loading="fichaAtual.salvando" :disable="!!pendentes(fichaAtual)" @click="confirmar(fichaAtual)">
              <q-tooltip v-if="pendentes(fichaAtual)">{{ pendentes(fichaAtual) }}</q-tooltip>
            </q-btn>
          </template>
          <template v-else>
            <q-btn v-if="aluHashid && criadas.length" flat no-caps icon="picture_as_pdf" label="PDF com todas as fichas" :loading="gerandoPdf" @click="pdf" />
            <q-space />
            <q-btn v-if="proxima" unelevated rounded no-caps class="bg-def text-white" icon-right="arrow_forward" :label="`Ir para ${rotulo(proxima)}`" @click="aba = proxima.chave" />
            <q-btn v-else unelevated rounded no-caps class="bg-def text-white" label="Concluir" @click="fechar" />
          </template>
        </q-card-actions>
      </template>
    </q-card>

    <!-- escolher / trocar exercício do catálogo -->
    <q-dialog v-model="escolha.aberta">
      <q-card style="width: 520px; max-width: 96vw; border-radius: 18px">
        <q-card-section class="titulo" style="font-size: 17px">
          {{ escolha.item && escolha.item.exercicio ? 'Trocar exercício' : 'Escolher exercício' }}
          <div v-if="escolha.item && escolha.item.nome_pedido" class="texto-suave" style="font-size: 12px; font-weight: 400">Você pediu: {{ escolha.item.nome_pedido }}</div>
        </q-card-section>
        <q-card-section class="q-pt-none">
          <q-input v-model="escolha.busca" outlined dense autofocus clearable placeholder="Buscar no catálogo" debounce="150">
            <template #prepend><q-icon name="search" /></template>
          </q-input>
          <q-list class="q-mt-sm lista-escolha">
            <q-item-label v-if="!escolha.busca && escolha.item && escolha.item.alternativas.length" header class="q-px-none">Sugestões da IA</q-item-label>
            <q-item v-for="x in resultados" :key="x.exe_hashid" clickable dense class="q-px-xs" @click="escolher(x)">
              <q-item-section avatar><div class="midia-p"><img v-if="x.exe_gif || x.exe_imagem" :src="x.exe_gif || x.exe_imagem" loading="lazy"></div></q-item-section>
              <q-item-section>
                <q-item-label>{{ x.exe_exercicio }}</q-item-label>
                <q-item-label caption>{{ x.mus_musculo }}</q-item-label>
              </q-item-section>
            </q-item>
            <div v-if="!resultados.length" class="texto-suave q-pa-sm">Nada encontrado.</div>
          </q-list>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-dialog>
</template>

<script setup>
import { computed, onBeforeUnmount, reactive, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'boot/api'
import { baixarPdf } from 'src/pdf'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  aluHashid: { type: String, default: null },
  alunoNome: { type: String, default: '' }
})
const emit = defineEmits(['update:modelValue', 'criadas'])
const $q = useQuasar()
const router = useRouter()

const exemplo = 'Ex.: TREINO A COSTAS: levantamento terra 3x10-15, puxada na barra 3x10-15, remada cabo... FICHA B PEITO: supino reto, crucifixo 3x12... FICHA C PERNAS: ...'
const mensagens = ['Lendo o seu pedido…', 'Procurando os exercícios no catálogo…', 'Separando as fichas…', 'Conferindo séries e repetições…']

const etapa = ref('pedido')
const pedido = ref('')
const passo = ref(0)
const proposta = ref({ resumo: '', avisos: [] })
const fichas = ref([])
const aba = ref(null)
const catalogo = ref({ musculos: [], metodos: [], pausas: [] })
const gerandoPdf = ref(false)
const verAvisos = ref(false)
const escolha = reactive({ aberta: false, ficha: null, item: null, busca: '' })
let relogio = null

const fichaAtual = computed(() => fichas.value.find((f) => f.chave === aba.value))
const criadas = computed(() => fichas.value.filter((f) => f.criada))
const proxima = computed(() => fichas.value.find((f) => !f.criada))
const todosExercicios = computed(() => catalogo.value.musculos.flatMap((m) => m.exercicios))

const rotulo = (f) => {
  const t = (f.titulo || '').split('·')[0].trim()
  return t.length > 18 ? t.slice(0, 17) + '…' : (t || 'Ficha')
}
const midia = (e) => e.exercicio && (e.exercicio.exe_gif || e.exercicio.exe_imagem)
const normalizar = (s) => (s || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase()
// o nome do catálogo não lembra o que foi pedido: mostra o pedido para o personal conferir
const naoBate = (e) => {
  const pedidoN = normalizar(e.nome_pedido).split(/\W+/).filter((p) => p.length > 3)
  const nome = normalizar(e.exercicio.exe_exercicio)
  return pedidoN.length && !pedidoN.some((p) => nome.includes(p.slice(0, 5)))
}
function pendentes (f) {
  if (!f.titulo.trim()) return 'Dê um título à ficha.'
  if (!f.exercicios.length) return 'A ficha precisa de pelo menos um exercício.'
  const faltam = f.exercicios.filter((e) => !e.exercicio).length
  return faltam ? `Escolha o exercício em ${faltam} item(ns).` : ''
}

async function sugerir () {
  etapa.value = 'carregando'
  passo.value = 0
  relogio = setInterval(() => passo.value++, 3500)
  try {
    const [{ data }] = await Promise.all([api.post('personal/ia/sugerir', { pedido: pedido.value }), carregarCatalogo()])
    proposta.value = { resumo: data.resumo, avisos: data.avisos }
    fichas.value = data.fichas.map((f) => ({ ...f, orientacoes: f.orientacoes || '', criada: null, salvando: false }))
    if (!fichas.value.length) {
      etapa.value = 'pedido'
      return $q.notify({ message: 'A IA não encontrou fichas nesse texto. Escreva os exercícios de cada ficha.', color: 'dark' })
    }
    aba.value = fichas.value[0].chave
    verAvisos.value = $q.screen.gt.sm
    etapa.value = 'conferir'
  } catch (erro) {
    etapa.value = 'pedido'
    $q.notify({ message: mensagemErro(erro, 'Não foi possível montar as fichas agora. Tente de novo.'), color: 'negative', timeout: 6000 })
  } finally {
    clearInterval(relogio)
  }
}

async function carregarCatalogo () {
  if (!catalogo.value.musculos.length) catalogo.value = (await api.get('personal/catalogo')).data
}

async function confirmar (f) {
  f.salvando = true
  try {
    const { data } = await api.post('personal/ia/confirmar', {
      alu_hashid: props.aluHashid,
      tre_titulo: f.titulo.trim(),
      tre_obs_professor: f.orientacoes.trim() || null,
      exercicios: f.exercicios.map((e) => ({
        exe_hashid: e.exercicio.exe_hashid,
        tex_serie: e.tex_serie || null,
        tex_repeticao: e.tex_repeticao || null,
        tex_carga: e.tex_carga || null,
        tex_tempo: e.tex_tempo || null,
        tex_obs: e.tex_obs || null,
        pau_hashid: e.pausa ? e.pausa.hashid : null,
        met_hashid: e.metodo ? e.metodo.hashid : null,
        cad_hashid: e.cadencia ? e.cadencia.hashid : null
      }))
    })
    f.criada = data.tre_hashid
    emit('criadas')
    $q.notify({ message: `${f.titulo} criada.`, color: 'dark', icon: 'check' })
    if (proxima.value) aba.value = proxima.value.chave
  } catch (erro) {
    $q.notify({ message: mensagemErro(erro, 'Não foi possível criar a ficha.'), color: 'negative', timeout: 6000 })
  } finally {
    f.salvando = false
  }
}

function descartar (f) {
  $q.dialog({ title: 'Descartar ficha', message: `"${f.titulo}" não será criada.`, cancel: 'Cancelar', ok: { label: 'Descartar', color: 'negative', flat: true } })
    .onOk(() => {
      fichas.value = fichas.value.filter((x) => x !== f)
      if (!fichas.value.length) etapa.value = 'pedido'
      else aba.value = (proxima.value || fichas.value[0]).chave
    })
}

function mover (f, i, passo) {
  const [x] = f.exercicios.splice(i, 1)
  f.exercicios.splice(i + passo, 0, x)
}

function adicionar (f) {
  const item = { chave: 'N' + Date.now(), nome_pedido: '', exercicio: null, alternativas: [], tex_serie: null, tex_repeticao: null, tex_carga: null, tex_tempo: null, tex_obs: null, pausa: null, metodo: null, cadencia: null }
  f.exercicios.push(item)
  trocar(f, f.exercicios[f.exercicios.length - 1])
}

function trocar (f, item) {
  Object.assign(escolha, { aberta: true, ficha: f, item, busca: '' })
}

const resultados = computed(() => {
  const item = escolha.item
  if (!item) return []
  const termo = normalizar(escolha.busca).trim()
  if (!termo) {
    // sem busca: sugestões da IA e os do mesmo músculo
    const musculo = item.exercicio && item.exercicio.mus_hashid
    const mesmos = musculo ? todosExercicios.value.filter((x) => x.mus_hashid === musculo) : []
    const vistos = new Set()
    return [...item.alternativas, ...mesmos].filter((x) => !vistos.has(x.exe_hashid) && vistos.add(x.exe_hashid)).slice(0, 60)
  }
  const partes = termo.split(/\s+/)
  return todosExercicios.value.filter((x) => {
    const nome = normalizar(x.exe_exercicio + ' ' + x.mus_musculo)
    return partes.every((p) => nome.includes(p))
  }).slice(0, 60)
})

function escolher (x) {
  escolha.item.exercicio = x
  escolha.aberta = false
}

function abrirFicha (f) {
  emit('update:modelValue', false)
  router.push({ name: 'personal-ficha', params: { treHashid: f.criada } })
}

async function pdf () {
  gerandoPdf.value = true
  try { await baixarPdf(`personal/alunos/${props.aluHashid}/pdf`) } catch (erro) {
    $q.notify({ message: 'Não foi possível gerar o PDF.', color: 'negative' })
  } finally { gerandoPdf.value = false }
}

function fechar () {
  const faltam = etapa.value === 'conferir' ? fichas.value.filter((f) => !f.criada).length : 0
  if (!faltam) return emit('update:modelValue', false)
  $q.dialog({ title: 'Fechar sem criar?', message: `${faltam} ficha(s) sugerida(s) ainda não foram criadas e serão perdidas.`, cancel: 'Continuar conferindo', ok: { label: 'Fechar', color: 'negative', flat: true } })
    .onOk(() => emit('update:modelValue', false))
}

function mensagemErro (erro, padrao) {
  const r = erro.response
  if (r && r.status === 429) return 'Muitos pedidos seguidos — espere um minuto e tente de novo.'
  return (r && r.data && r.data.message) || padrao
}

// ao reabrir depois de concluir, começa do zero (o texto do pedido fica)
watch(() => props.modelValue, (aberto) => {
  if (aberto && etapa.value === 'conferir' && !proxima.value) {
    etapa.value = 'pedido'
    fichas.value = []
  }
})

onBeforeUnmount(() => clearInterval(relogio))
</script>

<style scoped>
.ia-card { width: 900px; max-width: 96vw; border-radius: 18px; max-height: 92vh; }
.q-dialog__inner--maximized .ia-card { border-radius: 0; max-height: none; }
.abas { padding: 0 8px; }
.avisos { background: #FFF7DB; border: 1px solid #F1DE9A; border-radius: 12px; padding: 8px 10px; font-size: 12.5px; }
.avisos > div + div { margin-top: 4px; }
.criada { background: #EEF8EE; border: 1px solid #CBE8CB; border-radius: 14px; font-size: 14px; }
.sem-borda { border: 0; padding: 0; margin: 0; min-width: 0; }
.item { background: #fff; border: 1px solid var(--def-line); border-radius: 14px; padding: 10px; }
.item-falta { border-color: #E9A3A3; background: #FFF8F8; }
.midia { flex: none; width: 72px; height: 72px; border-radius: 10px; overflow: hidden; background: #F1F1EC; display: flex; align-items: center; justify-content: center; cursor: pointer; }
.midia img { width: 100%; height: 100%; object-fit: cover; display: block; }
.midia-p { width: 44px; height: 44px; border-radius: 8px; overflow: hidden; background: #F1F1EC; }
.midia-p img { width: 100%; height: 100%; object-fit: cover; }
.campo { flex: 1 1 90px; min-width: 90px; }
.campo-largo { flex: 2 1 160px; min-width: 150px; }
.lista-escolha { max-height: 52vh; overflow-y: auto; }
</style>
