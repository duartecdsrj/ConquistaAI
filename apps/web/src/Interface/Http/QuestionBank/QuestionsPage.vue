<template>
  <q-page class="page">
    <section class="top"><div><p class="eyebrow">BANCO PUBLICADO</p><h1>Questões</h1><p>Explore itens disponíveis para seus próximos cadernos.</p></div><q-btn flat no-caps color="primary" label="Limpar filtros" @click="clear" /></section>
    <q-card flat class="filters"><q-card-section class="filter-grid"><q-input v-model="filters.board" outlined dense label="Banca" @keyup.enter="search" /><q-input v-model.number="filters.year" outlined dense type="number" label="Ano" @keyup.enter="search" /><q-select v-model="filters.difficulty" clearable outlined dense label="Dificuldade" :options="difficultyOptions" emit-value map-options /><q-btn unelevated no-caps color="primary" label="Filtrar" :loading="loading" @click="search" /></q-card-section></q-card>
    <q-banner v-if="error" rounded class="error">{{ error }}</q-banner>
    <p class="results">{{ total }} {{ total === 1 ? 'questão encontrada' : 'questões encontradas' }}</p>
    <q-inner-loading :showing="loading" color="primary" />
    <q-list v-if="questions.length" bordered separator class="questions"><q-expansion-item v-for="(question, index) in questions" :key="question.id" group="questions" header-class="question-header"><template #header><q-item-section><span class="number">QUESTÃO {{ index + 1 }}</span><q-item-label class="statement">{{ question.statement }}</q-item-label><q-item-label caption>{{ question.board || 'Banca não informada' }} · {{ question.year || 'Ano não informado' }} · {{ difficulty(question.difficulty) }}</q-item-label></q-item-section></template><q-card flat class="options"><q-card-section><p v-for="option in question.options" :key="option.id"><strong>{{ option.label }}.</strong> {{ option.content }}</p></q-card-section></q-card></q-expansion-item></q-list>
    <q-card v-else-if="!loading" flat class="empty"><q-card-section><q-img src="/images/concursos-study-mark.png" width="84px" height="84px" fit="contain" /><div><h2>Nenhuma questão para estes filtros</h2><p>Tente remover filtros ou peça a inclusão de questões publicadas.</p></div></q-card-section></q-card>
  </q-page>
</template>
<script setup lang="ts">
import { onMounted, reactive } from 'vue'
import type { QuestionDifficulty } from '../../../Domain/QuestionBank/QuestionRepository'
import { useQuestions } from './useQuestions'
const { error, load, loading, questions, total } = useQuestions()
const filters = reactive<{ board: string; year: number | null; difficulty: QuestionDifficulty | null }>({ board: '', year: null, difficulty: null })
const difficultyOptions = [{ label: 'Fácil', value: 'EASY' }, { label: 'Média', value: 'MEDIUM' }, { label: 'Difícil', value: 'HARD' }]
function search(): Promise<void> { return load({ ...(filters.board.trim() ? { board: filters.board.trim() } : {}), ...(filters.year ? { year: filters.year } : {}), ...(filters.difficulty ? { difficulty: filters.difficulty } : {}) }) }
function clear(): void { filters.board = ''; filters.year = null; filters.difficulty = null; void search() }
function difficulty(value: QuestionDifficulty): string { return ({ EASY: 'Fácil', MEDIUM: 'Média', HARD: 'Difícil' })[value] }
onMounted(search)
</script>
<style scoped>
.page{max-width:1050px;margin:auto;padding:42px 34px}.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:23px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1{margin:0;color:#142950;font-size:32px}.top p:not(.eyebrow){margin:8px 0 0;color:#71819e}.filters,.questions,.empty{border:1px solid #e5ecf6;border-radius:17px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.04)}.filter-grid{display:grid;grid-template-columns:1.2fr .7fr 1fr auto;gap:12px}.filters .q-btn{border-radius:9px}.results{color:#72829d;font-size:13px;margin:20px 0 10px}.questions{overflow:hidden}.number{color:#2b6ed2;font-size:10px;font-weight:800;letter-spacing:.1em}.statement{margin:8px 0;color:#1b315a;font-size:16px;line-height:1.5}.options p{padding:10px 13px;margin:8px 0;border-radius:10px;background:#f7f9fd;color:#405174}.empty :deep(.q-card__section){display:flex;align-items:center;gap:22px;padding:32px}.empty h2{margin:0;color:#1a315a;font-size:19px}.empty p{color:#71819e}.error{margin-top:15px;background:#fff3f2;color:#ae2f25}@media(max-width:660px){.page{padding:28px 16px}.top{align-items:flex-start;flex-direction:column}.filter-grid{grid-template-columns:1fr}.filters .q-btn{height:42px}.empty :deep(.q-card__section){align-items:flex-start;flex-direction:column}}
</style>
