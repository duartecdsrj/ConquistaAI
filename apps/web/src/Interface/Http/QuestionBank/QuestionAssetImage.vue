<template>
  <img v-if="source" :src="source" alt="Figura da questão" />
</template>
<script setup lang="ts">
import { useQuestionAsset } from './useQuestionAsset'
const props = defineProps<{ url: string }>()
const { source } = useQuestionAsset(props.url)
</script>
