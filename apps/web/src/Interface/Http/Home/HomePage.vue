<template>
  <q-layout view="hHh Lpr fFf" class="shell">
    <q-header class="topbar"><q-toolbar><q-toolbar-title><span class="mark">C</span> Concursos</q-toolbar-title><q-avatar class="avatar" title="Sair" @click="emit('logout')">{{ initials }}</q-avatar></q-toolbar></q-header>
    <q-page-container><q-page class="page">
      <section class="welcome"><div><p class="eyebrow">BEM-VINDO</p><h1>Ola, {{ user.name }}</h1><p>Acompanhe sua evolucao e retome seus estudos.</p></div><q-btn unelevated no-caps class="primary" label="Continuar estudando" /></section>
      <section class="stats"><article v-for="item in stats" :key="item.label"><p>{{ item.label }}</p><strong>{{ item.value }}</strong><small>{{ item.detail }}</small></article></section>
      <section class="grid"><article class="panel"><h2>Sua meta semanal</h2><p>36 de 50 questoes concluidas</p><q-linear-progress rounded size="10px" :value=".72" color="primary" track-color="blue-1" /><strong class="score">72%</strong></article><article class="panel streak"><p>SEQUENCIA ATUAL</p><strong>7 dias</strong><small>Seu melhor: 12 dias</small></article></section>
      <section class="heading"><div><h2>Continue de onde parou</h2><p>Seus ultimos cadernos</p></div><q-btn flat no-caps label="Ver todos" /></section>
      <section class="books"><article v-for="book in notebooks" :key="book.title" class="book"><span>{{ book.state }}</span><h3>{{ book.title }}</h3><p>{{ book.questions }} questoes - {{ book.progress }}% concluido</p><q-linear-progress rounded size="7px" :value="book.progress / 100" :color="book.color" track-color="grey-3" /><q-btn unelevated no-caps :color="book.color" label="Continuar" /></article></section>
    </q-page></q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { AuthenticatedUser } from '../../../Domain/Identity/AuthRepository'

const props = defineProps<{ readonly user: AuthenticatedUser }>()
const emit = defineEmits<{ logout: [] }>()
const initials = computed(() => props.user.name.split(' ').filter(Boolean).slice(0, 2).map((name) => name[0]).join('').toUpperCase())
const stats = [{ label: 'Questoes respondidas', value: '128', detail: '+18 esta semana' }, { label: 'Taxa de acerto', value: '76%', detail: '+4% este mes' }, { label: 'Tempo de estudo', value: '8h 24m', detail: 'esta semana' }, { label: 'Revisoes pendentes', value: '12', detail: 'para hoje' }]
const notebooks = [{ state: 'EM ANDAMENTO', title: 'Redes de Computadores', questions: 30, progress: 62, color: 'primary' }, { state: 'EM ANDAMENTO', title: 'Seguranca da Informacao', questions: 25, progress: 40, color: 'deep-purple' }, { state: 'NOVO', title: 'Banco de Dados', questions: 20, progress: 0, color: 'teal' }]
</script>

<style scoped>
.shell{background:#f7f9fc;color:#1f2a44}.topbar{background:#fff;color:#1f2a44;border-bottom:1px solid #e9edf5}.mark{display:inline-grid;place-items:center;width:28px;height:28px;margin-right:8px;border-radius:9px;background:#3468e8;color:#fff;font-weight:800}.avatar{background:#e7efff;color:#2d5fd6;font-size:11px;font-weight:700;cursor:pointer}.page{max-width:1200px;margin:auto;padding:46px 30px}.welcome{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:30px}.eyebrow{color:#7e8ba1;font-size:11px;font-weight:700;letter-spacing:.08em;margin:0}.welcome h1{font-size:32px;margin:8px 0}.welcome p,.heading p{color:#718099;margin:0}.primary{background:#3468e8;color:#fff;border-radius:9px}.stats,.grid,.books{display:grid;gap:16px}.stats{grid-template-columns:repeat(4,1fr);margin-bottom:18px}.stats article,.panel,.book{padding:22px;border:1px solid #ebeff5;border-radius:15px;background:#fff;box-shadow:0 4px 16px rgba(30,49,92,.04)}.stats p,.stats small,.book p,.book span,.streak p,.streak small{display:block;margin:0;color:#748198;font-size:12px}.stats strong{display:block;font-size:24px;margin:5px 0}.grid{grid-template-columns:2fr 1fr}.panel h2{font-size:18px;margin:0 0 8px}.panel p{color:#758198}.score{display:block;margin-top:17px;font-size:25px;color:#285fcd}.streak{text-align:center;background:linear-gradient(145deg,#fff,#fff7ec)}.streak strong{display:block;font-size:30px;margin:12px}.heading{display:flex;justify-content:space-between;align-items:center;margin:34px 0 14px}.heading h2{font-size:18px;margin:0 0 4px}.books{grid-template-columns:repeat(3,1fr)}.book span{font-size:10px;font-weight:700}.book h3{margin:16px 0 6px;font-size:16px}.book .q-btn{width:100%;margin-top:16px;border-radius:9px}@media(max-width:800px){.stats,.books{grid-template-columns:repeat(2,1fr)}.grid{grid-template-columns:1fr}}@media(max-width:560px){.page{padding:28px 16px}.welcome{align-items:flex-start;flex-direction:column}.primary{width:100%}.stats,.books{grid-template-columns:1fr}}
</style>
