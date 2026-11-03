<template>
  <q-page class="pagina">
    <q-btn flat dense no-caps icon="arrow_back" label="Alunos" :to="{ name: 'personal' }" class="q-mb-sm" />

    <!-- aluno: dados, link de acesso e aparelhos -->
    <template v-if="aluHashid">
      <div v-if="aluno" class="cartao q-pa-md q-mb-md">
        <div class="row items-center">
          <div class="col">
            <h1 style="font-size: 22px">{{ aluno.alu_nome }}</h1>
            <div class="texto-suave" style="font-size: 13px">{{ aluno.telefones.join(' · ') }}</div>
          </div>
          <q-btn flat round icon="edit" @click="editar = true" />
        </div>
        <q-btn unelevated rounded no-caps class="bg-acc full-width q-mt-md" icon="send" label="Enviar acesso pelo WhatsApp" :loading="gerando" @click="gerarLink" />
        <q-expansion-item dense class="q-mt-sm" icon="smartphone" :label="`Aparelhos liberados (${aparelhos.length})`" @show="carregarAparelhos">
          <q-item v-for="ap in aparelhos" :key="ap.ace_hashid" dense>
            <q-item-section>
              <q-item-label style="font-size: 13px">{{ resumoAparelho(ap.ace_aparelho) }}</q-item-label>
              <q-item-label caption>Liberado {{ ap.liberado_em }} · último uso {{ ap.ultimo_uso || '-' }}</q-item-label>
            </q-item-section>
            <q-item-section side><q-btn flat dense no-caps color="negative" label="Desconectar" @click="revogar(ap)" /></q-item-section>
          </q-item>
          <div v-if="!aparelhos.length" class="texto-suave q-pa-sm" style="font-size: 13px">Nenhum aparelho liberado ainda.</div>
        </q-expansion-item>
      </div>
      <AlunoForm v-if="editar" :aluno="aluno" @fechar="editar = false" @salvo="(a) => { aluno = a; editar = false }" />
    </template>
    <div v-else class="q-mb-md">
      <span class="tag">Área livre</span>
      <h1 class="q-mt-sm" style="font-size: 22px">Fichas genéricas</h1>
      <p class="texto-suave">As marcadas como públicas aparecem para qualquer pessoa no app, sem link.</p>
    </div>

    <!-- fichas -->
    <div class="row items-center q-mb-sm">
      <h2 class="col" style="font-size: 18px">Fichas</h2>
      <q-btn v-if="aluHashid" flat no-caps icon="content_copy" label="Copiar genérica" @click="copiarGenerica" />
      <q-btn unelevated rounded no-caps class="bg-def text-white" icon="add" label="Nova" @click="novaFicha" />
    </div>
    <q-item v-for="f in fichas" :key="f.tre_hashid" clickable class="cartao q-mb-sm" :to="{ name: 'personal-ficha', params: { treHashid: f.tre_hashid } }">
      <q-item-section>
        <q-item-label class="titulo" style="font-size: 15px">{{ f.tre_titulo }}</q-item-label>
        <q-item-label caption>{{ f.qtd_exercicios }} exercício(s) · criada {{ f.tre_data_cadastro }}</q-item-label>
      </q-item-section>
      <q-item-section side v-if="!aluHashid"><q-badge :class="f.tre_publico ? 'bg-acc' : ''" :color="f.tre_publico ? undefined : 'grey-5'" :label="f.tre_publico ? 'pública' : 'privada'" /></q-item-section>
    </q-item>
    <div v-if="!fichas.length" class="texto-suave">Nenhuma ficha ainda.</div>

    <!-- link de acesso: um botão de WhatsApp por telefone -->
    <q-dialog v-model="mostrarLink">
      <q-card style="width: 460px; max-width: 94vw; border-radius: 18px">
        <q-card-section class="titulo" style="font-size: 18px">Enviar acesso para {{ aluno && aluno.alu_nome.split(' ')[0] }}</q-card-section>
        <q-card-section class="q-pt-none">
          <q-btn v-for="w in link.whatsapp" :key="w.tel_numero" unelevated rounded no-caps class="bg-def text-white full-width q-mb-sm" icon="chat" :label="`WhatsApp ${w.tel_numero}`" :href="w.url" target="_blank" />
          <div class="texto-suave q-mt-sm" style="font-size: 13px">Código para quem já tem o app instalado: <strong style="font-size: 18px; letter-spacing: 3px" class="text-dark">{{ link.codigo }}</strong></div>
          <div class="texto-suave" style="font-size: 12px">Vale uma vez, para um aparelho, por 7 dias. Para outro celular, gere outro link.</div>
          <q-btn flat dense no-caps icon="link" label="Copiar link" class="q-mt-sm" @click="copiar(link.link)" />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { copyToClipboard, useQuasar } from 'quasar'
import { api } from 'boot/api'
import AlunoForm from 'components/AlunoForm.vue'

const route = useRoute()
const router = useRouter()
const $q = useQuasar()
const aluHashid = computed(() => route.params.aluHashid || null)
const aluno = ref(null)
const fichas = ref([])
const aparelhos = ref([])
const editar = ref(false)
const gerando = ref(false)
const mostrarLink = ref(false)
const link = ref({ whatsapp: [] })

async function carregar () {
  if (aluHashid.value) aluno.value = (await api.get(`personal/alunos/${aluHashid.value}`)).data
  fichas.value = (await api.get('personal/fichas', { params: aluHashid.value ? { aluno: aluHashid.value } : {} })).data.fichas
}
async function carregarAparelhos () { aparelhos.value = (await api.get(`personal/alunos/${aluHashid.value}/aparelhos`)).data.aparelhos }

async function gerarLink () {
  gerando.value = true
  try { link.value = (await api.post(`personal/alunos/${aluHashid.value}/link`)).data; mostrarLink.value = true } finally { gerando.value = false }
}

function revogar (ap) {
  $q.dialog({ title: 'Desconectar aparelho', message: 'Esse aparelho deixa de ver as fichas exclusivas.', cancel: 'Cancelar', ok: { label: 'Desconectar', color: 'negative', flat: true } })
    .onOk(async () => { await api.delete(`personal/aparelhos/${ap.ace_hashid}`); carregarAparelhos() })
}

function novaFicha () {
  $q.dialog({ title: 'Nova ficha', prompt: { model: '', type: 'text', label: 'Título (ex.: Treino A · Superiores)' }, cancel: 'Cancelar', ok: { label: 'Criar', color: 'dark', flat: true } })
    .onOk(async (titulo) => {
      if (!titulo) return
      const { data } = await api.post('personal/fichas', { tre_titulo: titulo, alu_hashid: aluHashid.value })
      router.push({ name: 'personal-ficha', params: { treHashid: data.tre_hashid } })
    })
}

async function copiarGenerica () {
  const genericas = (await api.get('personal/fichas')).data.fichas
  if (!genericas.length) return $q.notify({ message: 'Nenhuma ficha genérica para copiar.', color: 'dark' })
  $q.dialog({ title: 'Copiar ficha genérica', options: { type: 'radio', model: genericas[0].tre_hashid, items: genericas.map((f) => ({ label: f.tre_titulo, value: f.tre_hashid })) }, cancel: 'Cancelar', ok: { label: 'Copiar', color: 'dark', flat: true } })
    .onOk(async (treHashid) => {
      const { data } = await api.post(`personal/fichas/${treHashid}/copiar`, { alu_hashid: aluHashid.value })
      router.push({ name: 'personal-ficha', params: { treHashid: data.tre_hashid } })
    })
}

function copiar (texto) { copyToClipboard(texto).then(() => $q.notify({ message: 'Link copiado.', color: 'dark' })) }
function resumoAparelho (ua = '') {
  if (/iPhone/.test(ua)) return 'iPhone'
  if (/Android/.test(ua)) return 'Android'
  return ua.split(' ')[0] || 'Aparelho'
}

onMounted(carregar)
</script>
