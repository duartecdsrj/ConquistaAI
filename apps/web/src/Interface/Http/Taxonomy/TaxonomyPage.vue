<template>
  <q-page class="taxonomy-page">
    <section class="page-heading">
      <div>
        <p class="eyebrow">ADMINISTRAÇÃO</p>
        <h1>Taxonomia de assuntos</h1>
        <p>Organize assuntos canônicos e seus relacionamentos.</p>
      </div>
      <q-btn flat no-caps color="primary" label="Atualizar" :loading="loading" @click="load" />
    </section>

    <q-banner v-if="error" rounded class="error-banner">{{ error }}</q-banner>

    <section class="taxonomy-grid">
      <q-card flat class="tree-card">
        <q-card-section>
          <h2>Árvore de conhecimento</h2>
          <q-inner-loading :showing="loading" />
          <q-tree v-if="tree.length" :nodes="tree" node-key="id" default-expand-all dense no-connectors />
          <p v-else-if="!loading" class="empty-state">Ainda não há assuntos canônicos.</p>
        </q-card-section>
      </q-card>

      <q-card flat class="form-card">
        <q-card-section>
          <p class="eyebrow">NOVO ASSUNTO</p>
          <h2>Adicionar à árvore</h2>
          <q-select v-model="editingId" outlined clearable emit-value map-options :options="parentOptions" label="Editar assunto existente" @update:model-value="selectSubject" />
          <q-form class="q-gutter-md" @submit.prevent="save">
            <q-input v-model="name" outlined label="Nome" :rules="[(value) => !!value || 'Informe o nome do assunto.']" />
            <q-select v-model="parentId" outlined clearable emit-value map-options :options="parentOptions" label="Assunto pai (opcional)" />
            <q-input v-model="description" outlined type="textarea" label="Descrição (opcional)" />
            <q-btn unelevated no-caps color="primary" type="submit"  :label="editingId ? 'Salvar alterações' : 'Criar assunto'" :loading="saving" />
          </q-form>
          <q-separator class="q-my-lg" />
          <q-form class="q-gutter-md" @submit.prevent="saveAlias">
            <p class="alias-title">Novo alias</p>
            <q-select v-model="aliasSubjectId" outlined emit-value map-options :options="parentOptions" label="Assunto canônico" />
            <q-input v-model="alias" outlined label="Variação do nome" />
            <q-btn outline no-caps color="primary" type="submit" label="Adicionar alias" :loading="saving" />
          </q-form>
        </q-card-section>
      </q-card>
    <q-card flat class="suggestions-card"><q-card-section><div class="row items-center justify-between"><h2>Possíveis duplicidades</h2><q-btn flat no-caps label="Analisar" color="primary" @click="loadSuggestions" /></div><q-list v-if="suggestions.length" separator><q-item v-for="item in suggestions" :key="item.sourceId + item.candidateId"><q-item-section><q-item-label>{{ item.sourceName }} · {{ item.candidateName }}</q-item-label><q-item-label caption>Similaridade de {{ Math.round(item.similarity * 100) }}%. Revisão humana necessária.</q-item-label></q-item-section></q-item></q-list><p v-else class="empty-state">Nenhuma sugestão carregada.</p></q-card-section></q-card>
    </section>
  </q-page>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTaxonomy } from './useTaxonomy'

const { create, createAlias, error, loadSuggestions, suggestions, update, load, loading, saving, subjects, tree } = useTaxonomy()
const name = ref('')
const parentId = ref<string | null>(null)
const description = ref('')
const editingId = ref<string | null>(null)
const alias = ref('')
const aliasSubjectId = ref<string | null>(null)
const parentOptions = computed(() => subjects.value.map((subject) => ({
  label: '· '.repeat(subject.level) + subject.name,
  value: subject.id,
})))

function selectSubject(id: string | null): void { const subject = subjects.value.find((item) => item.id === id); if (subject) { name.value = subject.name; parentId.value = subject.parentId; description.value = subject.description ?? '' } }

async function save(): Promise<void> {
  if (editingId.value ? await update(editingId.value, name.value, parentId.value, description.value || null) : await create(name.value, parentId.value, description.value || null)) {
    editingId.value = null
    name.value = ''
    parentId.value = null
    description.value = ''
  }
}

async function saveAlias(): Promise<void> {
  if (aliasSubjectId.value && await createAlias(aliasSubjectId.value, alias.value)) { alias.value = ''; aliasSubjectId.value = null }
}

onMounted(async () => { await load(); await loadSuggestions() })
</script>

<style scoped>
.taxonomy-page{max-width:1180px;margin:auto;padding:42px 34px}.page-heading{display:flex;justify-content:space-between;gap:20px;align-items:center;margin-bottom:24px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.page-heading h1,.form-card h2,.tree-card h2{margin:0;color:#142950}.page-heading p:not(.eyebrow){color:#71819e}.taxonomy-grid{display:grid;grid-template-columns:1.4fr .9fr;gap:18px}.tree-card,.form-card,.suggestions-card{min-height:330px;border:1px solid #e5ecf6;border-radius:18px}.tree-card h2,.form-card h2{font-size:20px;margin-bottom:20px}.alias-title{margin:0;color:#142950;font-weight:700}.empty-state{color:#71819e}.error-banner{margin-bottom:14px;background:#fff3f2;color:#ae2f25}@media(max-width:800px){.taxonomy-page{padding:28px 16px}.page-heading{align-items:flex-start;flex-direction:column}.taxonomy-grid{grid-template-columns:1fr}}
</style>
