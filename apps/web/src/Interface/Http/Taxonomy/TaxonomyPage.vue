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
          <q-form class="q-gutter-md" @submit.prevent="save">
            <q-input v-model="name" outlined label="Nome" :rules="[(value) => !!value || 'Informe o nome do assunto.']" />
            <q-select v-model="parentId" outlined clearable emit-value map-options :options="parentOptions" label="Assunto pai (opcional)" />
            <q-input v-model="description" outlined type="textarea" label="Descrição (opcional)" />
            <q-btn unelevated no-caps color="primary" type="submit" label="Criar assunto" :loading="saving" />
          </q-form>
        </q-card-section>
      </q-card>
    </section>
  </q-page>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useTaxonomy } from './useTaxonomy'

const { create, error, load, loading, saving, subjects, tree } = useTaxonomy()
const name = ref('')
const parentId = ref<string | null>(null)
const description = ref('')
const parentOptions = computed(() => subjects.value.map((subject) => ({
  label: '· '.repeat(subject.level) + subject.name,
  value: subject.id,
})))

async function save(): Promise<void> {
  if (await create(name.value, parentId.value, description.value || null)) {
    name.value = ''
    parentId.value = null
    description.value = ''
  }
}

onMounted(load)
</script>

<style scoped>
.taxonomy-page{max-width:1180px;margin:auto;padding:42px 34px}.page-heading{display:flex;justify-content:space-between;gap:20px;align-items:center;margin-bottom:24px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.page-heading h1,.form-card h2,.tree-card h2{margin:0;color:#142950}.page-heading p:not(.eyebrow){color:#71819e}.taxonomy-grid{display:grid;grid-template-columns:1.4fr .9fr;gap:18px}.tree-card,.form-card{min-height:330px;border:1px solid #e5ecf6;border-radius:18px}.tree-card h2,.form-card h2{font-size:20px;margin-bottom:20px}.empty-state{color:#71819e}.error-banner{margin-bottom:14px;background:#fff3f2;color:#ae2f25}@media(max-width:800px){.taxonomy-page{padding:28px 16px}.page-heading{align-items:flex-start;flex-direction:column}.taxonomy-grid{grid-template-columns:1fr}}
</style>
