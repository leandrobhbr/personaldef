<template>
  <q-layout view="hHh lpR fFf">
    <q-header class="topo text-dark">
      <q-toolbar class="q-px-md">
        <router-link :to="{ name: 'inicio' }" class="row items-center no-wrap text-dark" style="text-decoration: none; gap: 10px">
          <img src="icons/icon-128.png" alt="" width="34" height="34" style="border-radius: 8px">
          <div class="column" style="line-height: 1.1">
            <span class="titulo" style="font-size: 16px">Personal DEF</span>
            <span style="font-size: 11px; opacity: .7">{{ sessao.aluno ? sessao.aluno.alu_nome : 'Fichas de treino' }}</span>
          </div>
        </router-link>
        <q-space />
        <q-btn flat round icon="more_vert" aria-label="Menu">
          <q-menu auto-close>
            <q-list style="min-width: 230px">
              <q-item clickable @click="alternarPush" :disable="push === 'indisponivel'">
                <q-item-section avatar><q-icon :name="push === 'ativo' ? 'notifications_active' : 'notifications'" /></q-item-section>
                <q-item-section>{{ push === 'ativo' ? 'Avisos de nova versão ativos' : 'Ativar avisos de nova versão' }}</q-item-section>
              </q-item>
              <q-item clickable :to="{ name: 'entrar' }" v-if="!sessao.liberado">
                <q-item-section avatar><q-icon name="pin" /></q-item-section>
                <q-item-section>Tenho um código do Denilson</q-item-section>
              </q-item>
              <q-item clickable tag="a" href="https://personaldef.com.br" target="_blank">
                <q-item-section avatar><q-icon name="open_in_new" /></q-item-section>
                <q-item-section>Site personaldef.com.br</q-item-section>
              </q-item>
              <q-item clickable @click="sair" v-if="sessao.liberado">
                <q-item-section avatar><q-icon name="logout" /></q-item-section>
                <q-item-section>Desconectar este aparelho</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>

    <q-footer class="bg-white text-dark" bordered>
      <a href="https://bhnaweb.com.br" target="_blank" rel="noopener" class="row items-center justify-center no-wrap q-py-sm text-dark" style="text-decoration: none; gap: 8px; font-size: 13px">
        <span class="texto-suave">Desenvolvido por</span>
        <img src="bhnaweb.png" alt="" width="20" height="20">
        <strong>bhnaweb.com.br</strong>
      </a>
    </q-footer>
  </q-layout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import { api } from 'boot/api'
import { sessao } from 'src/sessao'
import { estadoPush, ativarPush } from 'src/push'

const $q = useQuasar()
const router = useRouter()
const push = ref('inativo')

onMounted(async () => { push.value = await estadoPush().catch(() => 'indisponivel') })

async function alternarPush () {
  if (push.value === 'ativo') return $q.notify({ message: 'Os avisos já estão ativos neste aparelho.', color: 'dark' })
  try {
    await ativarPush()
    push.value = 'ativo'
    $q.notify({ message: 'Pronto! Você será avisado quando sair versão nova.', color: 'positive', icon: 'check' })
  } catch (e) {
    $q.notify({ message: e.message, color: 'negative' })
  }
}

function sair () {
  $q.dialog({ title: 'Desconectar', message: 'Este aparelho volta a ver só as fichas públicas. Para voltar, peça um novo link ao Denilson.', cancel: 'Cancelar', ok: { label: 'Desconectar', color: 'negative', flat: true } })
    .onOk(async () => {
      await api.post('sair').catch(() => {})
      sessao.sair()
      router.push({ name: 'inicio' })
    })
}
</script>
