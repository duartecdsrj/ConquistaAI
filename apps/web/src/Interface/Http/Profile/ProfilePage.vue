<template>
  <q-page class="profile-page q-pa-md"><q-card flat class="profile-card"><q-card-section><p class="eyebrow">SEU PERFIL</p><h1>Foto de perfil</h1><p>Esta foto é privada e usada somente na sua conta.</p></q-card-section><q-card-section class="content"><q-avatar size="128px" color="blue-1" text-color="primary"><img v-if="url" :src="url" alt="Sua foto de perfil"><span v-else>{{ initials }}</span></q-avatar><div><q-file v-model="file" outlined accept="image/png,image/jpeg,image/webp" label="Escolher imagem" :disable="saving"><template #prepend><q-icon name="image" /></template></q-file><q-banner v-if="error" rounded class="error-banner q-mt-sm">{{ error }}</q-banner><q-btn unelevated no-caps color="primary" class="q-mt-md" label="Atualizar foto" :disable="!file" :loading="saving" @click="submit" /></div></q-card-section></q-card></q-page>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useProfileAvatar } from './useProfileAvatar'
const props = defineProps<{ readonly name: string }>()
const { url, error, load, replace, saving } = useProfileAvatar()
const file = ref<File | null>(null)
const initials = props.name.split(' ').filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase()
async function submit(): Promise<void> { if (file.value && await replace(file.value)) file.value = null }
onMounted(() => { void load() })
</script>
<style scoped>.profile-page{max-width:800px;margin:0 auto}.profile-card{border:1px solid #e2eaf6;border-radius:20px;background:#fff}.eyebrow{margin:0;color:#5577ad;font-size:11px;font-weight:800;letter-spacing:.1em}.profile-card h1{margin:7px 0;color:#18305c}.profile-card p:not(.eyebrow){color:#72839d}.content{display:flex;align-items:center;gap:30px}.content>div{width:min(390px,100%)}.error-banner{background:#fff0f0;color:#a31f2f}@media(max-width:599px){.content{align-items:stretch;flex-direction:column}}</style>
