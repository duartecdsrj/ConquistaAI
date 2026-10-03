<template>
  <q-page class="page">
    <q-banner v-if="error || catalogError" rounded class="error">{{ error || catalogError }}</q-banner>
    <q-card flat class="scope"><q-card-section><q-select v-model="selectedExamId" outlined dense emit-value map-options label="Concurso" :options="contestOptions" /></q-card-section></q-card>
    <q-linear-progress v-if="loading" indeterminate color="primary" class="map-loading" />
    <StudyMapGantt v-if="studyMap && selectedExamId" :map="studyMap" @save="saveMapSchedule" @period-change="changePeriod" />
    <q-card v-else-if="!loading" flat class="empty"><q-card-section><q-icon name="calendar_month" size="42px" color="primary" /><div><h2>Escolha um concurso</h2><p>Selecione um concurso para visualizar e montar seu cronograma.</p></div></q-card-section></q-card>
  </q-page>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import type { Exam } from '../../../Domain/Catalog/CatalogRepository'
import type { SaveStudyMapSchedule } from '../../../Domain/Performance/PerformanceRepository'
import { catalogUseCases } from '../../../Infrastructure/Container'
import StudyMapGantt from './StudyMapGantt.vue'
import { usePerformance } from './usePerformance'

const contests = ref<readonly Exam[]>([])
const selectedExamId = ref<string | null>(null)
const from = ref<string | null>(null)
const to = ref<string | null>(null)
const catalogError = ref('')
const { error, loadStudyMap, loading, saveStudyMapSchedule, studyMap } = usePerformance()
const contestOptions = computed(() => contests.value.map((item) => ({ label: item.name + (item.year ? ' · ' + item.year : ''), value: item.id })))

watch(selectedExamId, (examId) => { from.value = null; to.value = null; if (examId) void loadStudyMap(examId) })
function changePeriod(start: string | null, end: string | null): void { from.value = start; to.value = end; if (selectedExamId.value) void loadStudyMap(selectedExamId.value, start ?? undefined, end ?? undefined) }
function refresh(): void { if (selectedExamId.value) void loadStudyMap(selectedExamId.value, from.value ?? undefined, to.value ?? undefined) }
function saveMapSchedule(input: SaveStudyMapSchedule): void { void saveStudyMapSchedule(input) }

onMounted(() => {
  void catalogUseCases.listExams().then((items) => {
    contests.value = items
    const examId = items[0]?.id ?? null
    selectedExamId.value = examId
    if (examId) void loadStudyMap(examId)
  }).catch((reason: unknown) => { catalogError.value = reason instanceof Error ? reason.message : 'Não foi possível carregar os concursos.' })
})
</script>

<style scoped>
.page{max-width:none;margin:0;padding:14px 12px}.top{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:10px 0 25px;border-bottom:1px solid #dce7f6}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1,h2{margin:0;color:#142950}.top p{margin:7px 0;color:#71819e}.scope{margin-top:0;border:1px solid #dce7f6;border-radius:18px;background:rgba(255,255,255,.86)}.scope :deep(.q-card__section){max-width:440px}.map-loading{margin-top:14px;border-radius:99px}.error{margin-top:16px;background:#fff3f2;color:#ae2f25}.empty{margin-top:24px;border:1px solid #dce7f6;border-radius:18px}.empty :deep(.q-card__section){display:flex;align-items:center;gap:18px;padding:30px}.empty p{margin:6px 0 0;color:#71819e}@media(max-width:700px){.page{padding:28px 16px}.top{align-items:flex-start}.top .q-btn{flex:none}}
</style>
