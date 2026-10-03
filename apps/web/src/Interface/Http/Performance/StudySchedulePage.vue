<template>
  <q-page class="page">
    <section class="top">
      <div><p class="eyebrow">PLANEJAMENTO</p><h1>Seu cronograma de estudos</h1><p>Organize os assuntos por data, dependência e estado de estudo.</p></div>
      <q-btn flat no-caps color="primary" label="Atualizar" :loading="loading" @click="refresh" />
    </section>
    <q-banner v-if="error" rounded class="error">{{ error }}</q-banner>
    <q-card flat class="scope"><q-card-section><q-select v-model="selectedExamId" outlined dense emit-value map-options label="Concurso" :options="contestOptions" :loading="loadingContests" @update:model-value="changeContest" /></q-card-section></q-card>
    <q-inner-loading :showing="loading" color="primary" />
    <StudyMapGantt v-if="studyMap && selectedExamId" :map="studyMap" @save="saveMapSchedule" @period-change="changePeriod" />
    <q-card v-else-if="!loading" flat class="empty"><q-card-section><q-icon name="calendar_month" size="42px" color="primary" /><div><h2>Escolha um concurso</h2><p>Selecione um concurso para visualizar e montar seu cronograma.</p></div></q-card-section></q-card>
  </q-page>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import type { Exam } from '../../../Domain/Catalog/CatalogRepository'
import type { SaveStudyMapSchedule } from '../../../Domain/Performance/PerformanceRepository'
import { catalogUseCases } from '../../../Infrastructure/Container'
import StudyMapGantt from './StudyMapGantt.vue'
import { usePerformance } from './usePerformance'

const contests = ref<readonly Exam[]>([])
const selectedExamId = ref<string | null>(null)
const from = ref<string | null>(null)
const to = ref<string | null>(null)
const loadingContests = ref(false)
const { error, loadStudyMap, loading, saveStudyMapSchedule, studyMap } = usePerformance()
const contestOptions = computed(() => contests.value.map((item) => ({ label: item.name + (item.year ? ' · ' + item.year : ''), value: item.id })))

function changeContest(examId: string | null): void { from.value = null; to.value = null; if (examId) void loadStudyMap(examId) }
function changePeriod(start: string | null, end: string | null): void { from.value = start; to.value = end; if (selectedExamId.value) void loadStudyMap(selectedExamId.value, start ?? undefined, end ?? undefined) }
function refresh(): void { if (selectedExamId.value) void loadStudyMap(selectedExamId.value, from.value ?? undefined, to.value ?? undefined) }
function saveMapSchedule(input: SaveStudyMapSchedule): void { void saveStudyMapSchedule(input) }

onMounted(async () => {
  loadingContests.value = true
  try { contests.value = await catalogUseCases.listExams(); selectedExamId.value = contests.value[0]?.id ?? null; refresh() }
  finally { loadingContests.value = false }
})
</script>

<style scoped>
.page{max-width:1050px;margin:auto;padding:42px 34px}.top{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:10px 0 25px;border-bottom:1px solid #dce7f6}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1,h2{margin:0;color:#142950}.top p{margin:7px 0;color:#71819e}.scope{margin-top:22px;border:1px solid #dce7f6;border-radius:18px;background:rgba(255,255,255,.86)}.scope :deep(.q-card__section){max-width:440px}.error{margin-top:16px;background:#fff3f2;color:#ae2f25}.empty{margin-top:24px;border:1px solid #dce7f6;border-radius:18px}.empty :deep(.q-card__section){display:flex;align-items:center;gap:18px;padding:30px}.empty p{margin:6px 0 0;color:#71819e}@media(max-width:700px){.page{padding:28px 16px}.top{align-items:flex-start}.top .q-btn{flex:none}}
</style>
