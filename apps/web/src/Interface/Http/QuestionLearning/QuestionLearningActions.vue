<template>
  <div class="question-learning-actions" aria-label="Ações de aprendizagem">
    <q-btn flat dense round :color="interaction?.favorite ? 'amber-9' : 'grey-7'" :icon="interaction?.favorite ? 'star' : 'star_border'" :loading="saving" aria-label="Favoritar questão" @click="update({ favorite: !interaction?.favorite })"><q-tooltip>Favoritar</q-tooltip></q-btn>
    <q-btn flat dense round :color="interaction?.reviewLater ? 'primary' : 'grey-7'" :icon="interaction?.reviewLater ? 'bookmark' : 'bookmark_border'" :loading="saving" aria-label="Revisar depois" @click="update({ reviewLater: !interaction?.reviewLater })"><q-tooltip>Revisar depois</q-tooltip></q-btn>
    <q-btn flat dense round :color="interaction?.notMastered ? 'negative' : 'grey-7'" icon="school" :loading="saving" aria-label="Marcar como não dominada" @click="update({ notMastered: !interaction?.notMastered })"><q-tooltip>Não dominei</q-tooltip></q-btn>
    <q-spinner v-if="loading" size="18px" color="primary" />
    <span v-if="error" class="sr-only" role="alert">{{ error }}</span>
  </div>
</template>

<script setup lang="ts">
import { toRef } from 'vue'
import { useQuestionInteractions } from './useQuestionInteractions'
const props = defineProps<{ readonly questionId: string }>()
const { error, interaction, loading, saving, update } = useQuestionInteractions(toRef(props, 'questionId'))
</script>

<style scoped>
.question-learning-actions { display: inline-flex; align-items: center; gap: 2px; }
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
</style>
