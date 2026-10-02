<template>
  <q-page class="review-page">
    <header class="review-header"><div><p class="eyebrow">REVISÃO ADAPTATIVA</p><h1>{{ tab === 'session' ? 'Reforce o que importa' : 'Seu domínio' }}</h1><p>{{ tab === 'session' ? 'Recorde cada conceito antes de revelar a resposta.' : 'Evidências reais de revisões e cadernos.' }}</p></div><div class="header-actions"><q-btn v-if="tab === 'session'" flat no-caps icon="bolt" label="Revisão rápida" @click="sessionPage?.load('quick')"/><q-btn flat no-caps :icon="tab === 'session' ? 'account_tree' : 'style'" :label="tab === 'session' ? 'Mapa de domínio' : 'Revisar cards'" @click="tab = tab === 'session' ? 'mastery' : 'session'"/></div></header>
    <ReviewSessionPage v-if="tab === 'session'" ref="sessionPage" />
    <template v-else><q-banner v-if="error" rounded class="error q-mt-lg">{{ error }}</q-banner><q-card flat class="mastery-card"><q-card-section class="row items-center justify-between"><div><h2>Mapa de domínio</h2><p>Assuntos canônicos com evidência suficiente.</p></div><q-btn flat round icon="refresh" aria-label="Atualizar mapa de domínio" :loading="loading" @click="loadMastery()"/></q-card-section><q-separator dark/><q-card-section v-if="loading"><q-skeleton v-for="item in 4" :key="item" dark type="text" class="q-my-sm"/></q-card-section><q-card-section v-else-if="!mastery.length" class="mastery-empty">Ainda não há evidências suficientes para formar seu mapa de domínio.</q-card-section><q-list v-else separator dark><q-item v-for="node in mastery" :key="node.conceptId"><q-item-section><q-item-label>{{ node.name }}</q-item-label><q-item-label caption>{{ node.confidence === 'INSUFFICIENT' ? 'Dados insuficientes' : node.evidenceCount + ' evidências' }}</q-item-label></q-item-section><q-item-section side><q-badge :color="node.masteryScore === null ? 'grey-7' : 'primary'">{{ node.masteryScore === null ? '—' : Math.round(node.masteryScore) + '%' }}</q-badge></q-item-section></q-item></q-list></q-card></template>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import ReviewSessionPage from './ReviewSessionPage.vue'
import { useReview } from './useReview'
const tab = ref<'session' | 'mastery'>('session')
const sessionPage = ref<InstanceType<typeof ReviewSessionPage> | null>(null)
const { error, loadMastery, loading, mastery } = useReview()
onMounted(() => void loadMastery())
</script>

<style scoped>
.review-page{max-width:1040px;margin:auto;padding:38px 32px}.review-header{display:flex;align-items:center;justify-content:space-between;gap:22px;padding-bottom:22px;border-bottom:1px solid #1b3453}.eyebrow{margin:0 0 7px;color:#65b6ff;font-size:11px;font-weight:800;letter-spacing:.13em}.review-header h1{margin:0;color:#eaf4ff;font-size:30px;letter-spacing:-.045em}.review-header p:not(.eyebrow){margin:7px 0 0;color:#8ea6c8;font-size:13px}.header-actions{display:flex;gap:8px}.review-header :deep(.q-btn){border:1px solid #294b73;border-radius:10px;color:#b9d8ff}.mastery-card{margin-top:24px;border-radius:18px!important;background:#0c1b31!important}.mastery-card h2{margin:0;color:#eaf4ff;font-size:19px}.mastery-card p,.mastery-card :deep(.q-item__label--caption){color:#8ea6c8}.mastery-card :deep(.q-item__label){color:#dce9ff}.mastery-empty{padding:34px;color:#8ea6c8;text-align:center}.error{background:rgba(111,32,47,.42);color:#ffd8d4}@media(max-width:599px){.review-page{padding:22px 16px}.review-header{align-items:flex-start;flex-direction:column;gap:14px}.review-header h1{font-size:25px}.header-actions{width:100%;flex-direction:column}.review-header :deep(.q-btn){width:100%}}
</style>
