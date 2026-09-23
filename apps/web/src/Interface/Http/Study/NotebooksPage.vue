<template>
  <q-page class="page">
    <section class="top">
      <div><p class="eyebrow">SEUS ESTUDOS</p><h1>Cadernos</h1><p>Monte seleções congeladas de questões e volte quando quiser.</p></div>
      <q-btn unelevated no-caps color="primary" label="Novo caderno" @click="dialog = true" />
    </section>

    <q-banner v-if="error" rounded class="error">{{ error }}</q-banner>
    <q-inner-loading :showing="loading" color="primary" />

    <section v-if="!loading && notebooks.length" class="grid">
      <q-card v-for="book in notebooks" :key="book.id" flat class="book">
        <q-card-section>
          <div class="book-heading">
            <div class="mode">{{ book.mode === 'STUDY' ? 'ESTUDO GUIADO' : 'SIMULADO' }}</div>
            <q-badge :color="statusColor(book.status)" :label="statusLabel(book.status)" />
          </div>
          <h2>{{ book.name }}</h2>
          <p>{{ book.questionIds.length }} questões congeladas para esta sessão.</p>
          <div class="book-footer">
            <span>{{ formatted(book.createdAt) }}</span>
            <q-btn flat no-caps color="primary" :label="book.status === 'IN_PROGRESS' ? 'Retomar' : book.status === 'FINISHED' ? 'Finalizado' : 'Iniciar'" :disable="book.status === 'FINISHED'" @click="emit('open', book.id)" />
          </div>
        </q-card-section>
      </q-card>
    </section>

    <q-card v-else-if="!loading" flat class="empty">
      <q-card-section>
        <q-img src="/images/concursos-study-mark.png" width="100px" height="100px" fit="contain" />
        <div><h2>Organize sua prática</h2><p>Crie um caderno usando as questões publicadas no banco.</p><q-btn unelevated no-caps color="primary" label="Criar caderno" @click="dialog = true" /></div>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialog" persistent>
      <q-card class="dialog-card">
        <q-card-section><div class="dialog-title"><div><p class="eyebrow">NOVA SELEÇÃO</p><h2>Criar caderno</h2></div><q-btn flat round icon="close" aria-label="Fechar" @click="dialog = false" /></div></q-card-section>
        <q-form @submit.prevent="save">
          <q-card-section class="q-gutter-md">
            <q-input v-model="form.name" outlined label="Nome do caderno" :rules="[(value) => !!value || 'Informe um nome']" />
            <q-select v-model="form.mode" outlined label="Modo" :options="modeOptions" emit-value map-options />
            <q-input v-model.number="form.quantity" outlined type="number" min="1" max="100" label="Quantidade de questões" />
            <q-input v-model="form.board" outlined label="Banca (opcional)" />
            <q-input v-model.number="form.year" outlined type="number" label="Ano (opcional)" />
            <q-select v-model="form.difficulty" clearable outlined label="Dificuldade (opcional)" :options="difficultyOptions" emit-value map-options />
            <p class="hint">A API seleciona apenas questões publicadas e mantém a composição do caderno fixa.</p>
          </q-card-section>
          <q-card-actions align="right"><q-btn flat no-caps label="Cancelar" @click="dialog = false" /><q-btn unelevated no-caps color="primary" type="submit" label="Criar caderno" :loading="saving" /></q-card-actions>
        </q-form>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import type { NotebookMode, NotebookStatus } from '../../../Domain/Study/StudyRepository'
import { useNotebooks } from './useNotebooks'

const emit = defineEmits<{ open: [id: string] }>()
const { create, error, load, loading, notebooks, saving } = useNotebooks()
const dialog = ref(false)
const form = reactive<{ name: string; mode: NotebookMode; quantity: number; board: string; year: number | null; difficulty: '' | 'EASY' | 'MEDIUM' | 'HARD' }>({ name: '', mode: 'STUDY', quantity: 10, board: '', year: null, difficulty: '' })
const modeOptions = [{ label: 'Estudo guiado', value: 'STUDY' }, { label: 'Simulado', value: 'EXAM' }]
const difficultyOptions = [{ label: 'Fácil', value: 'EASY' }, { label: 'Média', value: 'MEDIUM' }, { label: 'Difícil', value: 'HARD' }]

async function save(): Promise<void> {
  const created = await create({
    name: form.name,
    mode: form.mode,
    quantity: form.quantity,
    filters: {
      ...(form.board.trim() ? { board: form.board.trim() } : {}),
      ...(form.year ? { year: form.year } : {}),
      ...(form.difficulty ? { difficulty: form.difficulty } : {}),
    },
  })
  if (!created) return
  dialog.value = false
  form.name = ''
  form.board = ''
  form.year = null
  form.difficulty = ''
}

function statusLabel(status: NotebookStatus): string {
  return status === 'IN_PROGRESS' ? 'EM ANDAMENTO' : status === 'FINISHED' ? 'FINALIZADO' : 'PRONTO PARA INICIAR'
}

function statusColor(status: NotebookStatus): string {
  return status === 'IN_PROGRESS' ? 'positive' : status === 'FINISHED' ? 'grey-6' : 'primary'
}

function formatted(value: string): string {
  return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'medium' }).format(new Date(value))
}

onMounted(load)
</script>

<style scoped>
.page{max-width:1240px;margin:auto;padding:42px 34px}.top{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:29px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1,.dialog-card h2{margin:0;color:#142950;font-size:32px}.top p:not(.eyebrow){margin:9px 0 0;color:#71819e}.top .q-btn{border-radius:10px}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.book,.empty{border:1px solid #e5ecf6;border-radius:18px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.045)}.book{min-height:214px;border-top:3px solid #4b7cf0}.book-heading{display:flex;justify-content:space-between;gap:8px}.mode{color:#2b6ed2;font-size:10px;font-weight:800;letter-spacing:.1em}.book h2{margin:18px 0 7px;color:#19315b;font-size:19px}.book p,.hint{color:#71819e;line-height:1.55}.book-footer{display:flex;align-items:center;justify-content:space-between;margin-top:22px;color:#8290a9;font-size:12px}.empty :deep(.q-card__section){display:flex;align-items:center;gap:24px;padding:37px}.empty h2{margin:0;color:#19315b}.empty p{color:#71819e}.dialog-card{width:min(520px,calc(100vw - 32px));border-radius:18px}.dialog-title{display:flex;justify-content:space-between}.dialog-card h2{font-size:23px}.hint{margin:0;font-size:13px}.error{margin-bottom:14px;background:#fff3f2;color:#ae2f25}@media(max-width:850px){.grid{grid-template-columns:1fr 1fr}}@media(max-width:560px){.page{padding:28px 16px}.top{align-items:flex-start;flex-direction:column}.top .q-btn{width:100%}.grid{grid-template-columns:1fr}.empty :deep(.q-card__section){align-items:flex-start;flex-direction:column}}
</style>
