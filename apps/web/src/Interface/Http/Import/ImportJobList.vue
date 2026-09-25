<template>
  <q-list separator>
    <q-item v-for="job in jobs" :key="job.id" class="job">
      <q-item-section>
        <div class="job-head"><div><q-item-label class="file">{{ job.documentOriginalName }}</q-item-label><q-item-label caption>{{ statusLabel(job.status) }} · {{ job.pageCount || '—' }} páginas analisadas</q-item-label></div><div class="job-actions"><q-badge rounded :color="statusColor(job.status)">{{ job.progress }}%</q-badge><q-btn v-if="cancellable && isActive(job.status)" flat dense round color="negative" icon="stop_circle" aria-label="Interromper importação" @click="$emit('cancel', job.id)"><q-tooltip>Interromper importação</q-tooltip></q-btn></div></div>
        <q-linear-progress rounded size="8px" :value="job.progress / 100" :color="statusColor(job.status)" track-color="blue-1" class="q-mt-sm"/>
        <div class="metrics"><div><strong>{{ job.extractedQuestions }}</strong><span>extraídas</span></div><div><strong>{{ job.createdQuestions }}</strong><span>criadas</span></div><div><strong>{{ job.duplicateQuestions }}</strong><span>duplicadas</span></div><div><strong>{{ job.failedQuestions }}</strong><span>com erro</span></div><div><strong>{{ job.classifiedQuestions }}</strong><span>classificadas</span></div><div><strong>{{ job.createdTaxonomySubjects }}</strong><span>assuntos novos</span></div><div><strong>{{ job.candidatePages }}</strong><span>páginas candidatas</span></div></div>
        <q-banner v-if="job.errorMessage" dense rounded class="job-error">{{ job.errorMessage }}</q-banner><q-item-label caption class="q-mt-sm">Criado em {{ new Date(job.createdAt).toLocaleString('pt-BR') }}</q-item-label>
      </q-item-section>
    </q-item>
  </q-list>
</template>
<script setup lang="ts">
import type { QuestionPdfImportJob } from '../../../Domain/Import/ImportRepository'
defineProps<{ readonly jobs: readonly QuestionPdfImportJob[]; readonly cancellable?: boolean }>()
defineEmits<{ cancel: [id: string] }>()
function statusLabel(status: string): string { return ({ PENDING: 'Na fila', PROCESSING: 'Processando', COMPLETED: 'Concluído', FAILED: 'Falhou', CANCELLED: 'Interrompido' } as Record<string, string>)[status] ?? status }
function isActive(status: string): boolean { return status === 'PENDING' || status === 'PROCESSING' }
function statusColor(status: string): string { return ({ PENDING: 'orange', PROCESSING: 'primary', COMPLETED: 'positive', FAILED: 'negative', CANCELLED: 'grey' } as Record<string, string>)[status] ?? 'grey' }
</script>
<style scoped>.job{padding:18px 0}.job-head{display:flex;justify-content:space-between;gap:12px}.job-actions{display:flex;align-items:center;gap:4px}.file{font-weight:700;color:#102a5c}.metrics{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;margin-top:15px}.metrics div{padding:9px;border-radius:10px;background:#f5f8fd}.metrics strong,.metrics span{display:block}.metrics strong{color:#0d55d7;font-size:17px}.metrics span{color:#6b7e9d;font-size:11px}.job-error{margin-top:12px;background:#fff1f0;color:#b42318}@media(max-width:700px){.metrics{grid-template-columns:repeat(2,1fr)}}</style>
