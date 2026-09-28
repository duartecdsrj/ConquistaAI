<template>
  <section class="proposal-preview" aria-label="Prévia da questão proposta">
    <p class="preview-label">PRÉVIA PARA APROVAÇÃO</p>
    <div class="preview-statement"><QuestionStatementWithAssets :statement="proposal.statement" :assets="previewAssets" /></div>
    <div class="preview-options" role="list" aria-label="Alternativas propostas">
      <div v-for="option in proposal.options" :key="option.id" class="preview-option" role="listitem">
        <q-icon name="radio_button_unchecked" size="18px" />
        <strong>{{ option.label }}.</strong>
        <QuestionContent :value="option.content" />
      </div>
    </div>
  </section>
</template>
<script setup lang="ts">
import { computed } from "vue"
import type { QuestionCorrectionRequest } from "../../../Domain/QuestionBank/QuestionRepository"
import QuestionContent from "./QuestionContent.vue"
import QuestionStatementWithAssets from "./QuestionStatementWithAssets.vue"
const props = defineProps<{ readonly proposal: NonNullable<QuestionCorrectionRequest["proposal"]>; readonly requestId: string }>()
const previewAssets = computed(() => { const figures = props.proposal.figures ?? []; return figures.length ? figures.map((_, index) => `admin/question-correction-requests/${props.requestId}/asset-preview/${index + 1}`) : props.proposal.asset_page ? [`admin/question-correction-requests/${props.requestId}/asset-preview`] : [] })
</script>

<style scoped>
.proposal-preview{margin-top:16px;padding:16px;border:1px solid #dce7f6;border-radius:16px;background:#f8fbff}.preview-label{margin:0 0 12px;color:#6682ad;font-size:10px;font-weight:800;letter-spacing:.09em}.preview-statement{margin-bottom:14px;color:#10275b;font-weight:600}.preview-options{display:grid;gap:8px}.preview-option{display:grid;grid-template-columns:auto auto minmax(0,1fr);align-items:start;gap:9px;padding:11px 12px;border:1px solid #dce6f3;border-radius:12px;background:#fff;color:#344e77}.preview-option strong{color:#173768}.preview-option :deep(.content){min-width:0}.preview-option :deep(.content p){margin:0}
</style>
