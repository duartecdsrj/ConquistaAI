<template>
  <q-layout view="lHh Lpr lFf" class="app-shell">
    <q-header class="app-header">
      <q-toolbar class="header-content">
        <q-btn flat round dense icon="menu" class="lt-md" aria-label="Abrir navegação" @click="drawer = !drawer" />
        <div class="mobile-brand lt-md"><q-img src="/images/concursos-study-mark.png" width="30px" height="30px" fit="contain" /><strong>ConquistaAI</strong></div>
        <q-space />
        <q-input v-model="headerSearch" class="header-search gt-sm" dense borderless placeholder="Buscar no sistema..." aria-label="Buscar no sistema">
          <template #prepend><q-icon name="search" size="17px" /></template>
          <template #append><span class="search-shortcut">Ctrl + K</span></template>
        </q-input>
        <q-space class="gt-sm" />
        <q-btn flat round dense icon="search" class="lt-md" aria-label="Buscar" />
        <q-btn flat round dense icon="notifications_none" class="gt-xs" aria-label="Notificações" />
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
const headerSearch = ref('')
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
.app-shell{background:#f5f9ff;color:#10275b}.app-header{background:rgba(255,255,255,.94);color:#10275b;border-bottom:1px solid #e4edf9;backdrop-filter:blur(12px)}.header-content{min-height:70px;padding:0 28px 0 274px}.header-search{width:min(390px,34vw);border:1px solid #e3ebf7;border-radius:8px;background:#f8fbff;padding:0 10px}.search-shortcut{color:#8ba0bf;font-size:10px}.mobile-brand{align-items:center;gap:7px;color:#10275b;font-size:15px}.user-name{color:#667b9c;font-size:13px;margin-right:10px}.profile-avatar{margin:0 4px 0 7px;background:#1469f5;color:#fff;font-size:12px;font-weight:800}.navigation-drawer{background:#fff;z-index:3000!important}.drawer-brand{display:flex;align-items:center;gap:9px;height:72px;padding:15px 20px;border-bottom:1px solid #eaf0f8}.drawer-brand strong{display:block;font-size:14px}.drawer-brand small{color:#8294af;font-size:9px}.navigation-list{padding:12px 10px}.q-item{min-height:35px;margin:3px 0;border-radius:6px;color:#435d89}.q-item :deep(.q-item__section--avatar){min-width:28px;color:#315eaa}.q-item__label{font-size:12px;font-weight:600}.nav-active{background:#e8f1ff!important;color:#1469f5!important}.drawer-footer{position:absolute;bottom:14px;left:12px;right:12px;display:flex;align-items:center;gap:9px;padding:12px;border:1px solid #e4edf8;border-radius:10px;background:#fbfdff;color:#55709b}.drawer-footer div{flex:1;min-width:0}.drawer-footer strong,.drawer-footer small{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.drawer-footer strong{font-size:12px;color:#1d3868}.drawer-footer small{font-size:10px;color:#8193ad}@media(max-width:899px){.header-content{padding:0 12px}.header-content .q-space:first-of-type{display:none}.navigation-drawer{top:0!important;height:100vh!important}.drawer-brand{height:92px;padding:20px}.drawer-footer{bottom:20px}.q-item{min-height:44px;margin:5px 0}.q-item__label{font-size:14px}.navigation-list{padding:16px 12px}}
</style>
