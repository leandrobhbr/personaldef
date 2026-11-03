<template>
  <q-page class="pagina">
    <div class="row items-center q-mb-md">
      <div class="col">
        <span class="tag">Área do personal</span>
        <h1 class="q-mt-sm" style="font-size: 24px">Alunos</h1>
      </div>
      <q-btn unelevated rounded class="bg-def text-white" icon="person_add" label="Novo aluno" no-caps @click="editar = { alu_nome: '', telefones: [''] }" />
    </div>

    <q-btn outline rounded no-caps class="full-width q-mb-md" icon="public" label="Fichas genéricas (área livre)" :to="{ name: 'personal-genericas' }" />

    <q-input v-model="busca" outlined dense clearable placeholder="Buscar aluno" class="q-mb-md"><template #prepend><q-icon name="search" /></template></q-input>
    <q-spinner v-if="carregando" color="dark" size="28px" />
    <q-item v-for="a in filtrados" :key="a.alu_hashid" clickable class="cartao q-mb-sm" :to="{ name: 'personal-aluno', params: { aluHashid: a.alu_hashid } }">
      <q-item-section avatar><q-avatar class="bg-acc">{{ a.alu_nome[0] }}</q-avatar></q-item-section>
      <q-item-section>
        <q-item-label class="titulo" style="font-size: 15px">{{ a.alu_nome }} <q-badge v-if="a.alu_situacao === 'IN'" color="grey" label="inativo" /></q-item-label>
        <q-item-label caption>{{ a.qtd_fichas }} ficha(s) · {{ a.qtd_aparelhos }} aparelho(s) · {{ a.telefones.join(', ') }}</q-item-label>
      </q-item-section>
      <q-item-section side><q-icon name="chevron_right" /></q-item-section>
    </q-item>

    <AlunoForm v-if="editar" :aluno="editar" @fechar="editar = null" @salvo="(a) => { editar = null; $router.push({ name: 'personal-aluno', params: { aluHashid: a.alu_hashid } }) }" />
  </q-page>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { api } from 'boot/api'
import AlunoForm from 'components/AlunoForm.vue'

const alunos = ref([])
const busca = ref('')
const carregando = ref(true)
const editar = ref(null)
const filtrados = computed(() => alunos.value.filter((a) => !busca.value || a.alu_nome.toLowerCase().includes(busca.value.toLowerCase())))

onMounted(async () => {
  try { alunos.value = (await api.get('personal/alunos')).data.alunos } finally { carregando.value = false }
})
</script>
