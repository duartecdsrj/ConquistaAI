<template>
  <template v-for="(part, index) in parts" :key="`${index}-${part}`">
    <QuestionContent :value="part" />
    <div v-if="index < assets.length" class="statement-asset">
      <QuestionAssetImage :url="assets[index]" />
    </div>
  </template>
  <div v-if="parts.length === 1" class="statement-asset">
    <QuestionAssetImage v-for="url in assets" :key="url" :url="url" />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import QuestionAssetImage from './QuestionAssetImage.vue'
import QuestionContent from './QuestionContent.vue'

const props = defineProps<{ readonly statement: string; readonly assets?: readonly string[] }>()
const assets = computed(() => props.assets ?? [])
const parts = computed(() => props.statement.split(/\[\[FIGURA(?::\d+)?\]\]/))
</script>

<style scoped>
.statement-asset{display:grid;gap:12px;margin:16px 0}.statement-asset :deep(img){max-width:100%;max-height:520px;object-fit:contain;border:1px solid #dce6f3;border-radius:10px}
</style>
