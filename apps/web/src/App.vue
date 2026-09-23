<template>
  <q-inner-loading :showing="loading" label="Carregando sessao..." />
  <LoginPage v-if="!loading && !authenticated" :submitting="submitting" :error="error" @submit="login" />
  <HomePage v-if="!loading && user" :user="user" @logout="logout" />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import HomePage from './Interface/Http/Home/HomePage.vue'
import LoginPage from './Interface/Http/Identity/LoginPage.vue'
import { useAuth } from './Interface/Http/Identity/useAuth'

const { authenticated, error, loading, login, logout, restore, submitting, user } = useAuth()
onMounted(restore)
</script>
