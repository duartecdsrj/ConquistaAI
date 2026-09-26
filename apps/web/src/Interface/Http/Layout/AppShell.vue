<template>
  <q-layout view="lHh Lpr lFf" class="app-shell">
    <q-header class="app-header">
      <q-toolbar class="header-content">
        <q-btn flat round dense icon="menu" class="lt-md" aria-label="Abrir navegação" @click="drawer = !drawer" />
        <div class="mobile-brand lt-md"><q-img src="/images/concursos-study-mark.png" width="30px" height="30px" fit="contain" /><strong>ConquistaAI</strong></div>
        <q-space />
        <span class="user-name gt-xs">{{ user.name }}</span>
        <q-avatar class="profile-avatar">{{ initials }}</q-avatar>
        <q-btn flat dense no-caps icon="logout" class="logout-button gt-sm" label="Sair" @click="emit('logout')" />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="drawer" show-if-above bordered :breakpoint="900" :width="252" class="navigation-drawer">
      <div class="drawer-brand"><q-img src="/images/concursos-study-mark.png" width="42px" height="42px" fit="contain" /><div><strong>ConquistaAI</strong><small>Estude. Evolua. Conquiste.</small></div></div>
      <q-list padding class="navigation-list">
        <q-item v-for="item in navigationItems" :key="item.id" clickable :active="active === item.id" active-class="nav-active" @click="navigate(item.id)">
          <q-item-section avatar><q-icon :name="item.icon" size="17px" /></q-item-section><q-item-section><q-item-label>{{ item.label }}</q-item-label></q-item-section>
        </q-item>
      </q-list>
      <div class="drawer-footer"><q-avatar color="blue-2" text-color="primary" size="38px">{{ initials }}</q-avatar><div><strong>{{ user.name }}</strong><small>{{ user.email }}</small></div><q-icon name="chevron_right" /></div>
    </q-drawer>

    <q-page-container><slot /></q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import type { AuthenticatedUser } from '../../../Domain/Identity/AuthRepository'

export type ApplicationSection = 'home' | 'notebooks' | 'questions' | 'performance' | 'assistant' | 'catalog' | 'import' | 'editorial' | 'taxonomy' | 'discovery'

const props = defineProps<{ readonly user: AuthenticatedUser; readonly active: ApplicationSection; readonly canManage: boolean }>()
const emit = defineEmits<{ navigate: [section: ApplicationSection]; logout: [] }>()
const $q = useQuasar()
const drawer = ref(!$q.screen.lt.md)
const initials = computed(() => props.user.name.split(' ').filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase())
const navigationItems = computed<readonly { id: ApplicationSection; label: string; caption: string; icon: string }[]>(() => [
  { id: 'home', label: 'Início', caption: 'Visão geral', icon: 'home_outlined' },
  { id: 'assistant', label: 'Assistente', caption: 'Pergunte ao edital', icon: 'diversity_3' },
  { id: 'notebooks', label: 'Cadernos e plano', caption: 'Monte e retome estudos', icon: 'menu_book' },
  { id: 'questions', label: 'Questões', caption: 'Banco publicado', icon: 'article_outlined' },
  { id: 'performance', label: 'Desempenho', caption: 'Resultados reais', icon: 'insights' },
  ...(props.canManage ? [
    { id: 'catalog' as const, label: 'Catálogo', caption: 'Administração', icon: 'inventory_2_outlined' },
    { id: 'discovery' as const, label: 'Descobertas', caption: 'Provas e gabaritos', icon: 'travel_explore' },
    { id: 'import' as const, label: 'Importar questões', caption: 'PDFs em lote e arquivos', icon: 'cloud_upload_outlined' },
    { id: 'editorial' as const, label: 'Revisar questões', caption: 'Publicação editorial', icon: 'fact_check' },
    { id: 'taxonomy' as const, label: 'Taxonomia', caption: 'Assuntos canônicos', icon: 'account_tree' },
  ] : []),
])

function navigate(section: ApplicationSection): void {
  emit('navigate', section)
  if ($q.screen.lt.md) drawer.value = false
}

watch(() => $q.screen.lt.md, (isMobile) => { drawer.value = !isMobile })
</script>

<style scoped>
.app-shell{background:transparent;color:var(--ink)}.app-header{background:rgba(248,252,255,.78);color:var(--ink);border-bottom:1px solid rgba(191,207,232,.7);backdrop-filter:blur(18px)}.header-content{min-height:70px;padding:0 30px 0 278px}.mobile-brand{align-items:center;gap:8px;color:var(--ink);font-size:15px}.user-name{color:#60769b;font-size:13px;margin-right:10px}.profile-avatar{margin:0 4px 0 7px;background:linear-gradient(145deg,#1d73ff,#7862eb);color:#fff;font-size:12px;font-weight:800;box-shadow:0 5px 12px rgba(54,103,214,.22)}.navigation-drawer{overflow:hidden;background:linear-gradient(170deg,rgba(255,255,255,.96),rgba(237,245,255,.92));z-index:3000!important}.navigation-drawer:before{content:'';position:absolute;right:-72px;top:108px;width:190px;height:190px;border-radius:50%;background:radial-gradient(circle,rgba(105,89,235,.12),transparent 67%)}.drawer-brand{position:relative;display:flex;align-items:center;gap:10px;height:82px;padding:17px 20px;border-bottom:1px solid #e5edf8}.drawer-brand strong{display:block;font-size:14px}.drawer-brand small{color:#8294af;font-size:9px}.navigation-list{position:relative;padding:18px 10px}.q-item{min-height:38px;margin:5px 0;border-radius:11px;color:#496288;transition:transform .18s ease,background .18s ease}.q-item:hover{transform:translateX(3px);background:rgba(227,238,255,.72)}.q-item :deep(.q-item__section--avatar){min-width:29px;color:#456cb4}.q-item__label{font-size:12px;font-weight:650}.nav-active{background:linear-gradient(90deg,#e0ecff,#edf0ff)!important;color:#1769f6!important;box-shadow:inset 3px 0 0 #1769f6}.drawer-footer{position:absolute;bottom:14px;left:12px;right:12px;display:flex;align-items:center;gap:9px;padding:12px;border:1px solid rgba(213,226,246,.9);border-radius:15px;background:rgba(255,255,255,.7);color:#55709b}.drawer-footer div{flex:1;min-width:0}.drawer-footer strong,.drawer-footer small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.drawer-footer strong{font-size:12px;color:#1d3868}.drawer-footer small{font-size:10px;color:#8193ad}@media(max-width:899px){.header-content{padding:0 12px}.navigation-drawer{top:0!important;height:100vh!important}.drawer-brand{height:96px;padding:21px}.drawer-footer{bottom:20px}.q-item{min-height:46px;margin:6px 0;border-radius:12px}.q-item__label{font-size:14px}.navigation-list{padding:16px 12px}}
</style>
