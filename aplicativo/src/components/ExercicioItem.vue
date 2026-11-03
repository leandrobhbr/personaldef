<template>
  <div class="cartao q-pa-md q-mb-sm" :style="item.tex_grupo_cor ? `border-left: 6px solid ${item.tex_grupo_cor}` : ''">
    <div class="row no-wrap" style="gap: 12px">
      <div class="midia" v-if="midia" @click="ampliar = true">
        <img :src="midia" :alt="nome" loading="lazy">
      </div>
      <div class="col">
        <div class="row items-start no-wrap">
          <span class="ordem">{{ indice + 1 }}</span>
          <div class="col">
            <div class="titulo" style="font-size: 16px; line-height: 1.25">{{ nome }}</div>
            <div class="texto-suave" style="font-size: 12px" v-if="item.musculo">{{ item.musculo.nome }}</div>
          </div>
        </div>
        <div class="row q-mt-sm" style="gap: 6px">
          <q-chip v-if="item.tex_serie || item.tex_repeticao" dense square class="bg-def text-acc" icon="repeat">{{ [item.tex_serie, item.tex_repeticao].filter(Boolean).join(' × ') }}</q-chip>
          <q-chip v-if="item.tex_carga" dense square outline icon="fitness_center">{{ item.tex_carga }}</q-chip>
          <q-chip v-if="item.tex_tempo" dense square outline icon="timer">{{ item.tex_tempo }}</q-chip>
          <q-chip v-if="item.pausa" dense square outline icon="hourglass_bottom" clickable @click="info(item.pausa)">{{ item.pausa.nome }}</q-chip>
          <q-chip v-if="item.metodo" dense square outline icon="bolt" clickable @click="info(item.metodo)">{{ item.metodo.nome }}</q-chip>
          <q-chip v-if="item.cadencia" dense square outline icon="speed" clickable @click="info(item.cadencia)">{{ item.cadencia.nome }}</q-chip>
        </div>
      </div>
    </div>
    <p v-if="item.tex_obs" class="q-mt-sm q-mb-none" style="white-space: pre-line"><q-icon name="sticky_note_2" class="q-mr-xs" />{{ item.tex_obs }}</p>
    <div class="row q-mt-sm" style="gap: 8px" v-if="exe && (exe.exe_video || exe.exe_explicacao)">
      <q-btn v-if="exe.exe_video" dense flat no-caps icon="play_circle" label="Ver vídeo" :href="exe.exe_video" target="_blank" />
      <q-btn v-if="exe.exe_explicacao" dense flat no-caps icon="menu_book" :label="aberto ? 'Fechar explicação' : 'Como fazer'" @click="aberto = !aberto" />
    </div>
    <q-slide-transition>
      <p v-show="aberto" class="texto-suave q-mt-sm q-mb-none" style="white-space: pre-line">{{ exe && exe.exe_explicacao }}</p>
    </q-slide-transition>

    <q-dialog v-model="ampliar">
      <q-card style="max-width: 92vw">
        <img :src="midia" :alt="nome" style="display: block; max-width: 92vw; max-height: 80vh">
        <q-card-section class="titulo">{{ nome }}</q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useQuasar } from 'quasar'

const props = defineProps({ item: { type: Object, required: true }, indice: { type: Number, default: 0 } })
const $q = useQuasar()
const aberto = ref(false)
const ampliar = ref(false)
const exe = computed(() => props.item.exercicio)
const nome = computed(() => exe.value?.exe_exercicio || 'Exercício')
const midia = computed(() => exe.value?.exe_gif || exe.value?.exe_imagem)

function info (o) {
  if (o.descricao) $q.dialog({ title: o.nome, message: o.descricao, ok: { label: 'Entendi', flat: true, color: 'dark' } })
}
</script>

<style scoped>
.midia { flex: none; width: 92px; height: 92px; border-radius: 12px; overflow: hidden; background: #F1F1EC; cursor: zoom-in; }
.midia img { width: 100%; height: 100%; object-fit: cover; display: block; }
.ordem { flex: none; width: 24px; height: 24px; margin-right: 8px; border-radius: 50%; background: var(--def-acc); color: var(--def-ink); font-weight: 800; font-size: 12px; display: flex; align-items: center; justify-content: center; }
</style>
