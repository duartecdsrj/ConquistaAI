<template>
  <q-page class="page">
    <section class="top"><div><p class="eyebrow">ANÁLISE REAL</p><h1 class="page-title">Seu desempenho</h1><p>Resultados calculados a partir das suas tentativas concluídas.</p></div><q-btn flat no-caps color="primary" label="Atualizar" :loading="loading" @click="refresh" /></section>
    <q-banner v-if="error" rounded class="error">{{ error }}</q-banner>
    <q-inner-loading :showing="loading" color="primary" />
    <template v-if="statistics && !loading">
      <section class="metrics"><q-card flat><q-card-section><span>Respostas concluídas</span><strong>{{ statistics.total }}</strong></q-card-section></q-card><q-card flat class="positive"><q-card-section><span>Acertos</span><strong>{{ statistics.correct }}</strong></q-card-section></q-card><q-card flat class="negative"><q-card-section><span>Erros</span><strong>{{ statistics.incorrect }}</strong></q-card-section></q-card><q-card flat><q-card-section><span>Tempo médio</span><strong>{{ duration }}</strong></q-card-section></q-card></section>
      <q-card flat class="summary"><q-card-section><p class="eyebrow">TAXA GERAL</p><h2>{{ percentage }}% de acerto</h2></q-card-section></q-card>
    </template>
    <section class="heading"><div><h2>Desempenho por escopo</h2><p>Escolha um concurso para a visão consolidada ou um edital para detalhar uma seleção específica.</p></div></section>
    <q-card v-if="dashboard" flat class="subjects"><q-card-section class="scope-controls"><q-select v-model="selectedExamId" outlined dense clearable emit-value map-options label="Concurso (visão consolidada)" :options="contestOptions" @update:model-value="changeContest" />
      <q-select v-model="selectedSyllabusId" outlined dense clearable emit-value map-options label="Edital (visão detalhada)" :options="syllabusOptions" @update:model-value="changeSyllabus" />
      <q-banner v-if="!dashboard.sufficientData" rounded class="warning">Dados insuficientes: {{ dashboard.total }} respostas em {{ dashboard.distinctDays }} dia(s). São necessárias 10 respostas em 3 dias.</q-banner>
    </q-card-section><q-card-section v-if="dashboard.subjects.length" class="subject-chart"><div v-for="subject in visibleSubjects" :key="subject.id" class="subject-row"><div class="subject-label"><strong>{{ subject.name }}</strong><span>{{ subject.correct }} de {{ subject.total }} acertos · {{ subject.distinctDays }} dia(s)</span></div><div class="subject-value"><strong>{{ Math.round(subject.percentage) }}%</strong><q-linear-progress rounded size="10px" :value="subject.percentage / 100" :color="subject.sufficientData ? 'primary' : 'blue-grey-4'" track-color="blue-1" /></div><q-badge :color="subject.sufficientData ? 'positive' : 'grey-6'">{{ subject.sufficientData ? 'Amostra suficiente' : 'Dados insuficientes' }}</q-badge></div></q-card-section><q-card-section v-else class="text-grey-7">Ainda não há respostas classificadas para o escopo escolhido.</q-card-section></q-card>
    <q-card v-else-if="!loading" flat class="empty"><q-card-section><q-img src="/images/concursos-study-mark.png" width="72px" height="72px" fit="contain" /><div><h2>Sem editais com tentativas concluídas</h2><p>Conclua questões para acompanhar seu desempenho por edital.</p></div></q-card-section></q-card>
  </q-page>
</template>
<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { catalogUseCases } from '../../../Infrastructure/Container'
import type { Exam } from '../../../Domain/Catalog/CatalogRepository'
import { usePerformance } from './usePerformance'
const props = defineProps<{ readonly initialExamId?: string | null }>()
const contests = ref<readonly Exam[]>([])
const selectedExamId = ref<string | null>(null)
const selectedSyllabusId = ref<string | null>(null)
const { dashboard, error, load, loadDashboard, loading, statistics } = usePerformance()
const percentage = computed(() => Math.round(statistics.value?.percentage ?? 0))
const duration = computed(() => { const seconds = Math.round(statistics.value?.averageElapsedSeconds ?? 0); return seconds > 59 ? String(Math.floor(seconds / 60)) + ' min' : String(seconds) + ' s' })
const contestOptions = computed(() => contests.value.map(item => ({ label: item.name + (item.year ? ' · ' + item.year : ''), value: item.id })))
const syllabusOptions = computed(() => (dashboard.value?.syllabi ?? []).map((item) => ({ label: item.examName + ' · ' + item.positionName + ' · ' + item.name, value: item.id })))
const visibleSubjects = computed(() => [...(dashboard.value?.subjects ?? [])].sort((a, b) => b.total - a.total || a.name.localeCompare(b.name, 'pt-BR')).slice(0, 12))
function changeContest(value: string | null): void { selectedSyllabusId.value = null; void loadDashboard(undefined, value ?? undefined) }
function changeSyllabus(value: string | null): void { selectedExamId.value = null; void loadDashboard(value ?? undefined) }
function refresh(): void { void load(); void loadDashboard(selectedSyllabusId.value ?? undefined, selectedExamId.value ?? undefined) }
onMounted(async () => { contests.value = await catalogUseCases.listExams(); selectedExamId.value = props.initialExamId ?? contests.value[0]?.id ?? null; refresh() })
watch(() => props.initialExamId, (value) => { if (value) { selectedExamId.value = value; selectedSyllabusId.value = null; void loadDashboard(undefined, value) } })
</script>
<style scoped>
.page{max-width:1050px;margin:auto;padding:42px 34px}.top{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:26px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1,h2{margin:0;color:#142950}.top p,.heading p{margin:7px 0;color:#71819e}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}.q-card{border:1px solid #e5ecf6;border-radius:17px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.04)}.metrics strong{display:block;margin-top:8px;color:#172e59;font-size:27px}.metrics span{color:#7888a4;font-size:12px}.summary{margin-top:18px}.heading{margin:34px 0 14px}.subjects{overflow:hidden}.scope-controls{display:grid;grid-template-columns:1fr 1fr;gap:12px}.subject-chart{display:grid;gap:14px}.subject-row{display:grid;grid-template-columns:minmax(0,1fr) minmax(160px,260px) auto;gap:16px;align-items:center;padding:12px;border:1px solid #e8eff8;border-radius:12px;background:#fbfdff}.subject-label{display:grid;gap:3px;color:#17305b}.subject-label span{color:#71819e;font-size:12px}.subject-value{display:grid;gap:6px;color:#205fbb;text-align:right}.subject-value strong{font-size:15px}.warning{margin-top:14px;background:#fff8e5;color:#765812}.empty :deep(.q-card__section){display:flex;align-items:center;gap:20px;padding:30px}.error{margin-bottom:14px;background:#fff3f2;color:#ae2f25}@media(max-width:700px){.page{padding:28px 16px}.metrics{grid-template-columns:1fr 1fr}.scope-controls{grid-template-columns:1fr}.subject-row{grid-template-columns:1fr}.subject-value{text-align:left}}@media(max-width:430px){.metrics{grid-template-columns:1fr}}

/* Desempenho: indicadores em faixa e leitura analítica com contraste suave. */
.top{padding:10px 0 25px;border-bottom:1px solid #dce7f6}.metrics{gap:0;margin-top:24px;border:1px solid #dce7f6;border-radius:22px;background:rgba(255,255,255,.76);overflow:hidden}.metrics .q-card{border:0!important;border-radius:0!important;box-shadow:none!important;background:transparent!important}.metrics .q-card+.q-card{border-left:1px solid #dce7f6!important}.metrics .q-card:nth-child(2n){background:rgba(236,245,255,.52)!important}.summary{border-radius:25px!important;background:linear-gradient(145deg,#fff,#f0f7ff)!important}.subjects{border-radius:23px!important;background:rgba(255,255,255,.76)!important}.subject-row{border:0;border-bottom:1px dashed #d9e5f4;border-radius:0;background:transparent}.subject-row:last-child{border-bottom:0}.scope-controls{padding:14px;border-radius:16px;background:#f4f8ff}@media(max-width:700px){.metrics .q-card:nth-child(2n){border-left:1px solid #dce7f6!important}.metrics .q-card:nth-child(n+3){border-top:1px solid #dce7f6!important}}
</style>
