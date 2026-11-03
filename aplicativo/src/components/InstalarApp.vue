<template>
  <div v-if="instalacao.disponivel" class="cartao q-pa-md q-mb-lg row items-center no-wrap" style="gap: 12px">
    <img src="icons/icon-128.png" alt="" width="44" height="44" style="border-radius: 10px">
    <div class="col">
      <div class="titulo" style="font-size: 15px">Instale o app no celular</div>
      <div class="texto-suave" style="font-size: 13px">Suas fichas a um toque, direto da tela inicial.</div>
    </div>
    <q-btn unelevated rounded no-caps class="bg-acc" icon="add_to_home_screen" label="Instalar" @click="tocar" />

    <q-dialog v-model="passoIos">
      <q-card class="q-pa-md" style="max-width: 360px; border-radius: 18px">
        <div class="titulo q-mb-sm" style="font-size: 18px">Instalar no iPhone</div>
        <ol class="q-pl-md q-my-none" style="line-height: 1.8">
          <li>Abra este endereço no <strong>Safari</strong></li>
          <li>Toque em <q-icon name="ios_share" size="18px" /> <strong>Compartilhar</strong>, na barra de baixo</li>
          <li>Escolha <strong>Adicionar à Tela de Início</strong></li>
        </ol>
        <div class="text-right q-mt-md"><q-btn flat no-caps label="Entendi" v-close-popup /></div>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { instalacao, instalar } from 'src/instalar'

const passoIos = ref(false)

async function tocar () {
  if (!(await instalar())) passoIos.value = true
}
</script>
