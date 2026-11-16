<template>
  <q-btn-dropdown flat dense no-caps icon="picture_as_pdf" :label="label" :loading="gerando" :disable="disable" auto-close>
    <q-list style="min-width: 250px">
      <q-item clickable @click="baixar('simplificado')">
        <q-item-section avatar><q-icon name="table_rows" /></q-item-section>
        <q-item-section>
          <q-item-label>Ficha simplificada</q-item-label>
          <q-item-label caption>Só tabelas, sem imagens · menos páginas</q-item-label>
        </q-item-section>
      </q-item>
      <q-item clickable @click="baixar('completo')">
        <q-item-section avatar><q-icon name="photo_library" /></q-item-section>
        <q-item-section>
          <q-item-label>Ficha completa</q-item-label>
          <q-item-label caption>Com as imagens de cada exercício</q-item-label>
        </q-item-section>
      </q-item>
    </q-list>
  </q-btn-dropdown>
</template>

<script setup>
// Botão "PDF" com os dois modelos: simplificado (tabelas) e completo (com as imagens dos exercícios)
import { ref } from 'vue'
import { useQuasar } from 'quasar'
import { baixarPdf } from 'src/pdf'

const props = defineProps({
  url: { type: String, required: true }, // ex.: personal/fichas/{hashid}/pdf
  label: { type: String, default: 'PDF' },
  disable: { type: Boolean, default: false }
})
const $q = useQuasar()
const gerando = ref(false)

async function baixar (modelo) {
  gerando.value = true
  try {
    await baixarPdf(`${props.url}?modelo=${modelo}`)
  } catch (e) {
    $q.notify({ message: 'Não foi possível gerar o PDF.', color: 'negative' })
  } finally {
    gerando.value = false
  }
}
</script>
