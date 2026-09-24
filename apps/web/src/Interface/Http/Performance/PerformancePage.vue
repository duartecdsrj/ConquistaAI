<template>
  <q-page class="page">
    <section class="top"><div><p class="eyebrow">ANÁLISE REAL</p><h1>Seu desempenho</h1><p>Resultados calculados a partir das suas tentativas concluídas.</p></div><q-btn flat no-caps color="primary" label="Atualizar" :loading="loading" @click="refresh" /></section>
    <q-banner v-if="error" rounded class="error">{{ error }}</q-banner>
    <q-inner-loading :showing="loading" color="primary" />
    <template v-if="statistics && !loading">
      <section class="metrics"><q-card flat><q-card-section><span>Respostas concluídas</span><strong>{{ statistics.total }}</strong></q-card-section></q-card><q-card flat class="positive"><q-card-section><span>Acertos</span><strong>{{ statistics.correct }}</strong></q-card-section></q-card><q-card flat class="negative"><q-card-section><span>Erros</span><strong>{{ statistics.incorrect }}</strong></q-card-section></q-card><q-card flat><q-card-section><span>Tempo médio</span><strong>{{ duration }}</strong></q-card-section></q-card></section>
      <q-card flat class="summary"><q-card-section><p class="eyebrow">TAXA GERAL</p><h2>{{ percentage }}% de acerto</h2></q-card-section></q-card>
    </template>
    <section class="heading"><div><h2>Dashboard por edital</h2><p>Inclui assuntos canônicos e seus descendentes.</p></div></section>
    <q-card v-if="dashboard" flat class="subjects"><q-card-section>
      <q-select outlined dense emit-value map-options label="Edital" :model-value="dashboard.selectedSyllabusId" :options="syllabusOptions" @update:model-value="changeSyllabus" />
      <q-banner v-if="!dashboard.sufficientData" rounded class="warning">Dados insuficientes: {{ dashboard.total }} respostas em {{ dashboard.distinctDays }} dia(s). São necessárias 10 respostas em 3 dias.</q-banner>
    </q-card-section><q-list v-if="dashboard.subjects.length" separator><q-item v-for="subject in dashboard.subjects" :key="subject.id"><q-item-section><q-item-label>{{ subject.name }}</q-item-label><q-item-label caption>{{ subject.correct }} acertos em {{ subject.total }} respostas · {{ subject.distinctDays }} dia(s)</q-item-label></q-item-section><q-item-section side><q-badge :color="subject.sufficientData ? 'positive' : 'grey-6'">{{ subject.sufficientData ? 'Amostra suficiente' : 'Dados insuficientes' }}</q-badge><strong>{{ Math.round(subject.percentage) }}%</strong></q-item-section></q-item></q-list><q-card-section v-else class="text-grey-7">Ainda não há respostas classificadas canonicamente neste edital.</q-card-section></q-card>
    <q-card v-else-if="!loading" flat class="empty"><q-card-section><q-img src="/images/concursos-study-mark.png" width="72px" height="72px" fit="contain" /><div><h2>Sem editais com tentativas concluídas</h2><p>Conclua questões para acompanhar seu desempenho por edital.</p></div></q-card-section></q-card>
  </q-page>
</template>
<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { usePerformance } from './usePerformance'
const { dashboard, error, load, loadDashboard, loading, statistics } = usePerformance()
const percentage = computed(() => Math.round(statistics.value?.percentage ?? 0))
const duration = computed(() => { const seconds = Math.round(statistics.value?.averageElapsedSeconds ?? 0); return seconds > 59 ? String(Math.floor(seconds / 60)) + ' min' : String(seconds) + ' s' })
const syllabusOptions = computed(() => (dashboard.value?.syllabi ?? []).map((item) => ({ label: item.examName + ' · ' + item.positionName + ' · ' + item.name, value: item.id })))
function changeSyllabus(value: string | null): void { if (value) void loadDashboard(value) }
function refresh(): void { void load(); void loadDashboard(dashboard.value?.selectedSyllabusId ?? undefined) }
onMounted(refresh)
</script>
<style scoped>
.page{max-width:1050px;margin:auto;padding:42px 34px}.top{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:26px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1,h2{margin:0;color:#142950}.top p,.heading p{margin:7px 0;color:#71819e}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}.q-card{border:1px solid #e5ecf6;border-radius:17px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.04)}.metrics strong{display:block;margin-top:8px;color:#172e59;font-size:27px}.metrics span{color:#7888a4;font-size:12px}.summary{margin-top:18px}.heading{margin:34px 0 14px}.subjects{overflow:hidden}.warning{margin-top:14px;background:#fff8e5;color:#765812}.empty :deep(.q-card__section){display:flex;align-items:center;gap:20px;padding:30px}.error{margin-bottom:14px;background:#fff3f2;color:#ae2f25}@media(max-width:700px){.page{padding:28px 16px}.metrics{grid-template-columns:1fr 1fr}}@media(max-width:430px){.metrics{grid-template-columns:1fr}}
</style>
