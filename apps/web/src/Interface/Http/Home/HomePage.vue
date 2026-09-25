<template>
  <q-page class="app-page">
    <section class="hero">
      <div><p class="eyebrow">PAINEL DE ESTUDOS</p><h1 class="page-title">Olá, {{ user.name.split(' ')[0] }}.</h1><p class="hero-copy">Seu ritmo se constrói questão por questão. Veja o que merece sua atenção hoje.</p></div>
      <div class="hero-mark"><q-img src="/images/concursos-study-mark.png" fit="contain" /><span>Preparação<br><strong>com propósito</strong></span></div>
    </section>
    <q-banner v-if="error" rounded class="error-banner">{{ error }} <template #action><q-btn flat no-caps label="Tentar novamente" @click="load" /></template></q-banner>
    <q-inner-loading :showing="loading" color="primary" label="Atualizando painel..." />
    <template v-if="!loading">
      <section class="metric-grid">
        <q-card flat class="metric-card"><q-card-section><span>Questões concluídas</span><strong>{{ statistics?.total ?? 0 }}</strong><small>{{ statistics?.correct ?? 0 }} acertos registrados</small></q-card-section></q-card>
        <q-card flat class="metric-card success"><q-card-section><span>Taxa de acerto</span><strong>{{ percentage }}%</strong><small>sobre tentativas concluídas</small></q-card-section></q-card>
        <q-card flat class="metric-card violet"><q-card-section><span>Tempo médio</span><strong>{{ elapsed }}</strong><small>por tentativa</small></q-card-section></q-card>
        <q-card flat class="metric-card amber"><q-card-section><span>Para praticar</span><strong>{{ notebookTotal }}</strong><small>cadernos disponíveis</small></q-card-section></q-card>
      </section>
      <section class="content-grid">
        <q-card flat class="progress-card"><q-card-section><div class="section-title"><div><p class="eyebrow">VISÃO GERAL</p><h2>Seu desempenho</h2></div><q-btn flat no-caps color="primary" label="Ver desempenho" @click="emit('navigate', 'performance')" /></div>
          <div class="progress-body"><div class="percentage-ring"><strong>{{ percentage }}%</strong><span>de acerto</span></div><div><h3>{{ statistics?.correct ?? 0 }} acertos em {{ statistics?.total ?? 0 }} respostas</h3><p>Continue praticando para transformar consistência em confiança.</p><q-linear-progress rounded size="9px" :value="percentage / 100" color="primary" track-color="blue-1" /></div></div>
        </q-card-section></q-card>
        <q-card flat class="goal-card"><q-card-section><q-img src="/images/concursos-study-mark.png" width="74px" height="74px" fit="contain" /><p class="eyebrow">PRÓXIMO PASSO</p><h2>Pratique com intenção</h2><p>Crie um caderno por assunto, banca ou nível de dificuldade.</p><q-btn unelevated no-caps color="primary" label="Criar caderno" @click="emit('navigate', 'notebooks')" /></q-card-section></q-card>
      </section>
      <section class="section-heading"><div><h2>Continue de onde parou</h2><p>Seus cadernos mais recentes</p></div><q-btn flat no-caps color="primary" label="Ver todos" @click="emit('navigate', 'notebooks')" /></section>
      <div v-if="notebooks.length" class="notebook-grid"><q-card v-for="book in notebooks" :key="book.id" flat class="notebook-card"><q-card-section><div class="notebook-mode">{{ book.mode === 'STUDY' ? 'ESTUDO' : 'SIMULADO' }}</div><h3>{{ book.name }}</h3><p>{{ book.questionIds.length }} questões selecionadas</p><q-btn unelevated no-caps color="primary" label="Abrir caderno" @click="emit('navigate', 'notebooks')" /></q-card-section></q-card></div>
      <q-card v-else flat class="empty-card"><q-card-section><q-img src="/images/concursos-study-mark.png" width="82px" height="82px" fit="contain" /><div><h2>Seu primeiro caderno começa aqui</h2><p>Escolha filtros e monte uma seleção de questões publicadas para estudar.</p><q-btn unelevated no-caps color="primary" label="Criar meu caderno" @click="emit('navigate', 'notebooks')" /></div></q-card-section></q-card>
    </template>
  </q-page>
</template>
<script setup lang="ts">
import { computed, onMounted } from 'vue'
import type { AuthenticatedUser } from '../../../Domain/Identity/AuthRepository'
import { useDashboard } from './useDashboard'
import type { ApplicationSection } from '../Layout/AppShell.vue'
defineProps<{ readonly user: AuthenticatedUser }>()
const emit = defineEmits<{ navigate: [section: ApplicationSection] }>()
const { error, load, loading, notebooks, notebookTotal, statistics } = useDashboard()
const percentage = computed(() => Math.round(statistics.value?.percentage ?? 0))
const elapsed = computed(() => { const value = Math.round(statistics.value?.averageElapsedSeconds ?? 0); return value > 59 ? String(Math.floor(value / 60)) + ' min' : String(value) + ' s' })
onMounted(load)
</script>
<style scoped>
.app-page{max-width:1240px;margin:auto;padding:42px 34px 60px}.hero{display:flex;align-items:center;justify-content:space-between;gap:25px;margin-bottom:28px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.hero h1{margin:0;color:#132750;font-size:36px;letter-spacing:-.04em}.hero-copy{max-width:510px;margin:11px 0 0;color:#71809b;line-height:1.6}.hero-mark{display:flex;align-items:center;gap:10px;padding:9px 17px 9px 10px;border:1px solid #dce8fb;border-radius:18px;background:linear-gradient(130deg,#fff,#eef5ff);color:#54709b;font-size:13px}.hero-mark :deep(.q-img){width:58px;height:58px}.metric-grid,.content-grid,.notebook-grid{display:grid;gap:16px}.metric-grid{grid-template-columns:repeat(4,1fr);margin-bottom:18px}.metric-card,.progress-card,.goal-card,.notebook-card,.empty-card{border:1px solid #e5ecf6;border-radius:18px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.045)}.metric-card{border-top:3px solid #4a7af0}.metric-card.success{border-top-color:#25a66e}.metric-card.violet{border-top-color:#8760e8}.metric-card.amber{border-top-color:#f5ae31}.metric-card span,.metric-card small{display:block;color:#7888a4;font-size:12px}.metric-card strong{display:block;margin:8px 0 6px;color:#162b57;font-size:27px}.content-grid{grid-template-columns:1.75fr 1fr}.section-title,.section-heading{display:flex;justify-content:space-between;align-items:start}.section-title h2,.section-heading h2{margin:0;color:#162a55;font-size:20px}.progress-body{display:grid;grid-template-columns:135px 1fr;align-items:center;gap:24px;padding:17px 4px 4px}.percentage-ring{display:grid;place-content:center;width:120px;height:120px;border:11px solid #ddecff;border-top-color:#2675d5;border-right-color:#2675d5;border-radius:50%;color:#1e5ea9;text-align:center}.percentage-ring strong{font-size:27px}.percentage-ring span{font-size:11px}.progress-body h3{margin:0;color:#1b315b;font-size:17px}.progress-body p,.goal-card p,.section-heading p,.notebook-card p,.empty-card p{color:#71819e;line-height:1.55}.goal-card{background:linear-gradient(145deg,#fff,#f5f9ff)}.goal-card h2{margin:10px 0 8px;color:#1b315c;font-size:20px}.goal-card .q-btn,.notebook-card .q-btn{border-radius:10px}.section-heading{margin:37px 0 14px;align-items:center}.section-heading p{margin:4px 0 0;font-size:13px}.notebook-grid{grid-template-columns:repeat(3,1fr)}.notebook-mode{color:#2a6bcf;font-size:10px;font-weight:800;letter-spacing:.08em}.notebook-card h3{margin:18px 0 6px;font-size:17px}.notebook-card .q-btn{width:100%;margin-top:12px}.empty-card :deep(.q-card__section){display:flex;align-items:center;gap:20px;padding:28px}.empty-card h2{margin:0;color:#1c315c;font-size:20px}.empty-card p{margin:7px 0 13px}.error-banner{margin-bottom:17px;background:#fff3f2;color:#ae2f25}@media(max-width:900px){.metric-grid{grid-template-columns:repeat(2,1fr)}.content-grid{grid-template-columns:1fr}.notebook-grid{grid-template-columns:1fr 1fr}}@media(max-width:600px){.app-page{padding:28px 16px 48px}.hero{align-items:flex-start;flex-direction:column}.hero h1{font-size:30px}.hero-mark{display:none}.metric-grid,.notebook-grid{grid-template-columns:1fr}.progress-body{grid-template-columns:1fr}.percentage-ring{margin:auto}.empty-card :deep(.q-card__section){align-items:flex-start;flex-direction:column}}
</style>
