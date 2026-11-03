<template>
  <q-item clickable :to="to" class="cartao q-pa-md q-mb-sm">
    <q-item-section avatar>
      <q-avatar class="bg-def text-acc" rounded icon="fitness_center" />
    </q-item-section>
    <q-item-section>
      <q-item-label class="titulo" style="font-size: 16px">{{ ficha.tre_titulo }}</q-item-label>
      <q-item-label caption>{{ ficha.qtd_exercicios }} exercício{{ ficha.qtd_exercicios === 1 ? '' : 's' }}</q-item-label>
    </q-item-section>
    <q-item-section side><q-icon name="chevron_right" /></q-item-section>
  </q-item>
</template>

<script setup>
defineProps({ ficha: { type: Object, required: true }, to: { type: Object, required: true } })
</script>
