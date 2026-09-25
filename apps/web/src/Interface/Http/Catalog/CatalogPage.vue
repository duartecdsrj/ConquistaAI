<template>
  <q-page class="page">
    <section class="top">
      <div><p class="eyebrow">ADMINISTRAÇÃO</p><h1 class="page-title">Catálogo</h1><p>Organize concursos, cargos, editais, assuntos e tags.</p></div>
      <q-btn unelevated no-caps color="primary" icon="add" label="Novo concurso" @click="dialog = true" />
    </section>
    <q-banner v-if="error" rounded class="error">{{ error }}</q-banner>
    <q-card flat class="catalog-card">
      <q-tabs v-model="tab" dense align="left" active-color="primary" indicator-color="primary" class="catalog-tabs">
        <q-tab name="contests" icon="workspace_premium" label="Concursos" /><q-tab name="positions" icon="badge" label="Cargos" /><q-tab name="notices" icon="description" label="Editais" /><q-tab name="subjects" icon="category" label="Assuntos" /><q-tab name="tags" icon="sell" label="Tags" />
      </q-tabs>
      <q-separator />
      <q-card-section>
        <template v-if="tab === 'contests'">
          <div class="filters"><q-input v-model="filter" dense outlined placeholder="Buscar concursos..." clearable><template #prepend><q-icon name="search" /></template></q-input><q-btn outline color="primary" icon="tune" aria-label="Filtros" /></div>
          <q-table flat :rows="filteredExams" :columns="columns" row-key="id" :loading="loading" hide-bottom :pagination="{ rowsPerPage: 25 }" no-data-label="Nenhum concurso cadastrado.">
            <template #body-cell-name="props"><q-td :props="props"><div class="name-cell"><q-avatar size="30px" color="blue-1" text-color="primary" icon="workspace_premium" />{{ props.row.name }}</div></q-td></template>
            <template #body-cell-organizer="props"><q-td :props="props">{{ props.row.organizer || '—' }}<span v-if="props.row.year"> · {{ props.row.year }}</span></q-td></template>
            <template #body-cell-actions="props"><q-td :props="props"><q-btn flat round dense color="primary" icon="visibility"><q-tooltip>Ver concurso</q-tooltip></q-btn><q-btn flat round dense color="primary" icon="edit"><q-tooltip>Editar concurso</q-tooltip></q-btn></q-td></template>
          </q-table>
        </template>
        <div v-else class="coming"><q-icon name="construction" size="30px" color="primary" /><div><strong>{{ tabLabel }}</strong><p>Selecione um concurso para revisar os dados extraídos do edital.</p></div></div>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialog" persistent><q-card class="create-dialog"><q-card-section><div class="dialog-title">Novo concurso</div><p>Cadastre o concurso e, se disponível, anexe o PDF do edital para iniciar a extração.</p></q-card-section><q-card-section class="form"><q-input v-model="name" dense outlined label="Nome do concurso *" autofocus /><div class="form-row"><q-input v-model="organizer" dense outlined label="Organizadora" /><q-input v-model.number="year" dense outlined type="number" label="Ano" /></div><q-file v-model="document" dense outlined accept="application/pdf,.pdf" label="PDF do edital (opcional)" clearable><template #prepend><q-icon name="picture_as_pdf" /></template></q-file><q-banner rounded class="hint"><q-icon name="auto_awesome" color="primary" /> O edital será extraído e encaminhado para análise assistida de cargos, assuntos e pesos do caderno.</q-banner></q-card-section><q-card-actions align="right"><q-btn flat no-caps label="Cancelar" v-close-popup /><q-btn unelevated no-caps color="primary" label="Cadastrar concurso" :loading="saving" @click="submit" /></q-card-actions></q-card></q-dialog>
  </q-page>
</template>
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import type { QTableColumn } from 'quasar'
import { useCatalog } from './useCatalog'
const { createExamWithNotice, error, exams, loadExams, loading, saving } = useCatalog()
const dialog = ref(false); const tab = ref('contests'); const filter = ref(''); const name = ref(''); const organizer = ref(''); const year = ref<number | null>(null); const document = ref<File | null>(null)
const columns: QTableColumn[] = [{ name: 'name', label: 'Nome', field: 'name', align: 'left', sortable: true }, { name: 'organizer', label: 'Organizadora', field: 'organizer', align: 'left' }, { name: 'notices', label: 'Editais', field: () => '—', align: 'center' }, { name: 'positions', label: 'Cargos', field: () => '—', align: 'center' }, { name: 'questions', label: 'Questões', field: () => '—', align: 'center' }, { name: 'actions', label: 'Ações', field: () => '', align: 'right' }]
const filteredExams = computed(() => { const needle = filter.value.trim().toLocaleLowerCase(); return !needle ? exams.value : exams.value.filter((item) => [item.name, item.organizer || '', String(item.year || '')].join(' ').toLocaleLowerCase().includes(needle)) })
const tabLabel = computed(() => ({ positions: 'Cargos', notices: 'Editais', subjects: 'Assuntos', tags: 'Tags' }[tab.value] || 'Catálogo'))
async function submit(): Promise<void> { const created = await createExamWithNotice(name.value, organizer.value, year.value, document.value); if (!created) return; name.value = ''; organizer.value = ''; year.value = null; document.value = null; dialog.value = false }
onMounted(loadExams)
</script>
<style scoped>
.page{max-width:1080px;margin:auto;padding:40px 34px}.top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:24px}.eyebrow{margin:0 0 6px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1{margin:0;color:#142950;font-size:32px}.top p:not(.eyebrow){margin:7px 0 0;color:#71819e}.catalog-card{overflow:hidden;border:1px solid #e5ecf6;border-radius:15px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.04)}.catalog-tabs{min-height:52px}.filters{display:flex;gap:10px;margin-bottom:14px}.filters .q-field{flex:1}.name-cell{display:flex;align-items:center;gap:10px;font-weight:700;color:#18325c}.error{margin-bottom:14px;background:#fff3f2;color:#ae2f25}.coming{min-height:240px;display:flex;align-items:center;justify-content:center;gap:13px;color:#526985;text-align:left}.coming p{margin:5px 0 0;font-size:13px}.create-dialog{width:min(520px,calc(100vw - 32px));border-radius:16px}.dialog-title{color:#142950;font-size:21px;font-weight:800}.create-dialog p{margin:7px 0 0;color:#71819e;font-size:13px}.form{display:grid;gap:14px}.form-row{display:grid;grid-template-columns:1fr 120px;gap:12px}.hint{display:flex;gap:8px;align-items:flex-start;background:#eef6ff;color:#35537f;font-size:12px}@media(max-width:600px){.page{padding:28px 16px}.top{align-items:stretch;flex-direction:column}.top .q-btn{width:100%}.filters{align-items:stretch;flex-direction:column}.catalog-tabs{overflow-x:auto}.form-row{grid-template-columns:1fr}.top h1{font-size:28px}}
</style>
