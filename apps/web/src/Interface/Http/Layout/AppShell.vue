<template>
  <q-layout view="lHh Lpr lFf" class="app-shell">
    <q-header class="app-header">
      <q-toolbar class="header-content">
        <q-btn flat round dense icon="menu" class="lt-md" aria-label="Abrir navegação" @click="drawer = !drawer" />
        <div class="mobile-brand lt-md"><q-img src="/images/concursos-study-mark.png" width="30px" height="30px" fit="contain" /><strong>ConquistaAI</strong></div>
        <q-space />
        <span class="user-name gt-xs">{{ user.name }}</span>
        <q-avatar class="profile-avatar">{{ initials }}</q-avatar>
        <q-btn flat dense no-caps icon="logout" class="logout-button" label="Sair" @click="emit('logout')" />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="drawer" show-if-above bordered :breakpoint="900" :width="252" class="navigation-drawer">
      <div class="drawer-brand"><q-img src="/images/concursos-study-mark.png" width="42px" height="42px" fit="contain" /><div><strong>ConquistaAI</strong><small>Estude. Evolua. Conquiste.</small></div></div>
      <q-list padding>
        <q-item v-for="item in navigationItems" :key="item.id" clickable :active="active === item.id" active-class="nav-active" @click="navigate(item.id)">
          <q-item-section><q-item-label>{{ item.label }}</q-item-label><q-item-label caption>{{ item.caption }}</q-item-label></q-item-section>
        </q-item>
      </q-list>
      <div class="drawer-footer"><q-img src="/images/concursos-study-mark.png" width="54px" height="54px" fit="contain" /><p>Estude. Evolua.<br>Conquiste.</p></div>
    </q-drawer>

    <q-page-container><slot /></q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useQuasar } from 'quasar'
import type { AuthenticatedUser } from '../../../Domain/Identity/AuthRepository'

export type ApplicationSection = 'home' | 'notebooks' | 'questions' | 'performance' | 'catalog' | 'import' | 'editorial'

const props = defineProps<{ readonly user: AuthenticatedUser; readonly active: ApplicationSection; readonly canManage: boolean }>()
const emit = defineEmits<{ navigate: [section: ApplicationSection]; logout: [] }>()
const $q = useQuasar()
const drawer = ref(!$q.screen.lt.md)
const initials = computed(() => props.user.name.split(' ').filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase())
const navigationItems = computed<readonly { id: ApplicationSection; label: string; caption: string }[]>(() => [
  { id: 'home', label: 'Início', caption: 'Visão geral' },
  { id: 'notebooks', label: 'Cadernos', caption: 'Monte e retome estudos' },
  { id: 'questions', label: 'Questões', caption: 'Banco publicado' },
  { id: 'performance', label: 'Desempenho', caption: 'Resultados reais' },
  ...(props.canManage ? [
    { id: 'catalog' as const, label: 'Catálogo', caption: 'Administração' },
    { id: 'import' as const, label: 'Importar questões', caption: 'Preview e confirmação' },
    { id: 'editorial' as const, label: 'Revisar questões', caption: 'Publicação editorial' },
  ] : []),
])

function navigate(section: ApplicationSection): void {
  emit('navigate', section)
  if ($q.screen.lt.md) drawer.value = false
}

watch(() => $q.screen.lt.md, (isMobile) => { drawer.value = !isMobile })
</script>

<style scoped>
.app-shell{background:#f5f8fc;color:#16254a}.app-header{background:rgba(255,255,255,.94);color:#17264a;border-bottom:1px solid #e7edf7;backdrop-filter:blur(10px)}.header-content{min-height:68px;padding:0 28px 0 280px}.mobile-brand{align-items:center;gap:7px;color:#17305c;font-size:15px}.user-name{color:#667694;font-size:14px;margin-right:11px}.profile-avatar{margin-right:8px;background:#e6efff;color:#2460d4;font-size:12px;font-weight:800}.navigation-drawer{background:#fff;z-index:3000!important}.drawer-brand{display:flex;align-items:center;gap:10px;height:112px;padding:25px;border-bottom:1px solid #edf1f7}.drawer-brand strong{display:block;font-size:18px}.drawer-brand small{color:#8090aa;font-size:12px}.q-item{min-height:54px;margin:5px 10px;border-radius:11px;color:#52627f}.q-item__label{font-weight:650}.nav-active{background:#e7efff!important;color:#2164d9!important}.drawer-footer{position:absolute;bottom:20px;left:20px;right:20px;display:flex;align-items:center;gap:9px;padding:12px;border-radius:15px;background:#f3f7ff;color:#57709a}.drawer-footer p{font-size:12px;line-height:1.45;margin:0}@media(max-width:899px){.header-content{padding:0 12px}.logout-button{min-width:40px}.logout-button :deep(.q-btn__content){font-size:0}.logout-button :deep(.q-icon){font-size:20px}.navigation-drawer{top:0!important;height:100vh!important}.drawer-brand{padding:20px}.drawer-footer{display:none}}
</style>
