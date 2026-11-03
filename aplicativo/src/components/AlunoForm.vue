<template>
  <q-dialog :model-value="true" @hide="$emit('fechar')">
    <q-card style="width: 460px; max-width: 94vw; border-radius: 18px">
      <q-card-section class="titulo" style="font-size: 18px">{{ aluno.alu_hashid ? 'Editar aluno' : 'Novo aluno' }}</q-card-section>
      <q-card-section class="q-gutter-sm">
        <q-input v-model="form.alu_nome" outlined label="Nome" autofocus />
        <div class="texto-suave" style="font-size: 13px">Telefones (WhatsApp) — o link de acesso vai para eles</div>
        <div v-for="(t, i) in form.telefones" :key="i" class="row no-wrap items-center" style="gap: 6px">
          <q-input v-model="form.telefones[i]" outlined dense class="col" mask="(##) #####-####" unmasked-value inputmode="tel" :label="`Telefone ${i + 1}`" />
          <q-btn v-if="form.telefones.length > 1" flat round dense icon="close" @click="form.telefones.splice(i, 1)" />
        </div>
        <q-btn v-if="form.telefones.length < 5" flat dense no-caps icon="add" label="Outro telefone" @click="form.telefones.push('')" />
        <q-input v-model="form.alu_obs" outlined type="textarea" autogrow label="Observações (só você vê)" />
        <q-toggle v-if="aluno.alu_hashid" v-model="ativo" label="Aluno ativo (inativo perde o acesso)" color="dark" />
      </q-card-section>
      <q-card-actions align="right">
        <q-btn flat no-caps label="Cancelar" v-close-popup />
        <q-btn unelevated rounded no-caps class="bg-def text-white" label="Salvar" :loading="salvando" @click="salvar" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'boot/api'

const props = defineProps({ aluno: { type: Object, required: true } })
const emit = defineEmits(['fechar', 'salvo'])
const $q = useQuasar()
const form = reactive({ alu_nome: props.aluno.alu_nome, alu_obs: props.aluno.alu_obs || '', telefones: (props.aluno.telefones || ['']).map((t) => t.replace(/^55/, '')) })
const ativo = ref(props.aluno.alu_situacao !== 'IN')
const salvando = ref(false)

async function salvar () {
  salvando.value = true
  const dados = { ...form, telefones: form.telefones.filter(Boolean), alu_situacao: ativo.value ? 'AT' : 'IN' }
  try {
    const { data } = props.aluno.alu_hashid ? await api.put(`personal/alunos/${props.aluno.alu_hashid}`, dados) : await api.post('personal/alunos', dados)
    emit('salvo', data)
  } catch (e) {
    $q.notify({ message: e.response?.data?.message || 'Não foi possível salvar.', color: 'negative' })
  } finally { salvando.value = false }
}
</script>
