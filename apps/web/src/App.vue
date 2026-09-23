<template>
  <q-inner-loading :showing="loading" label="Carregando sessão..." />
  <LoginPage v-if="!loading && !authenticated" :submitting="submitting" :error="error" @submit="login" />
  <AppShell v-else-if="!loading && user" :user="user" :active="section" :can-manage="user.roles.includes('ADMIN')" @navigate="section = $event" @logout="logout">
    <HomePage v-if="section === 'home'" :user="user" @navigate="section = $event" />
    <NotebooksPage v-else-if="section === 'notebooks'" />
    <QuestionsPage v-else-if="section === 'questions'" />
    <CatalogPage v-else-if="section === 'catalog'" />
    <ImportPage v-else-if="section === 'import'" />
    <PerformancePage v-else />
  </AppShell>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import HomePage from './Interface/Http/Home/HomePage.vue'
import LoginPage from './Interface/Http/Identity/LoginPage.vue'
import AppShell, { type ApplicationSection } from './Interface/Http/Layout/AppShell.vue'
import NotebooksPage from './Interface/Http/Study/NotebooksPage.vue'
import QuestionsPage from './Interface/Http/QuestionBank/QuestionsPage.vue'
import PerformancePage from './Interface/Http/Performance/PerformancePage.vue'
import CatalogPage from './Interface/Http/Catalog/CatalogPage.vue'
import ImportPage from './Interface/Http/Import/ImportPage.vue'
import { useAuth } from './Interface/Http/Identity/useAuth'
const section = ref<ApplicationSection>('home')
const { authenticated, error, loading, login, logout, restore, submitting, user } = useAuth()
onMounted(restore)
</script>
