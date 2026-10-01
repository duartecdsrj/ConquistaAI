<template>
  <q-field outlined stack-label :label="label" class="subject-tree-select" @click="menu = true">
    <template #control>
      <div class="self-center full-width no-outline" tabindex="0">{{ summary }}</div>
    </template>
    <template #append><q-btn flat round dense icon="account_tree" aria-label="Abrir árvore de assuntos" @click="menu = true" /></template>
  </q-field>
  <q-menu v-model="menu" fit anchor="bottom left" self="top left" class="subject-tree-menu">
    <q-card flat><q-card-section class="tree-menu-head"><q-input v-model="filter" dense outlined clearable label="Buscar assunto" @click.stop><template #prepend><q-icon name="search" /></template></q-input><span>{{ modelValue.length }} selecionado(s)</span></q-card-section><q-separator/><q-card-section class="tree-scroll"><q-tree :nodes="tree" node-key="id" label-key="name" tick-strategy="strict" :filter="filter" :ticked="[...modelValue]" @update:ticked="update"><template #default-header="prop"><div class="tree-node"><q-icon :name="prop.node.children?.length ? 'folder' : 'article'" color="primary" size="18px"/><span class="tree-name">{{ prop.node.name }}</span><q-badge v-if="prop.node.questionCount !== undefined" class="question-count" :label="String(prop.node.questionCount) + ' questões'"/></div></template></q-tree></q-card-section></q-card>
  </q-menu>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

export interface SubjectTreeOption { readonly id: string; readonly name: string; readonly parentId: string | null; readonly questionCount?: number }
interface TreeNode extends SubjectTreeOption { readonly children: TreeNode[] }
const props = defineProps<{ readonly modelValue: readonly string[]; readonly options: readonly SubjectTreeOption[]; readonly label: string }>()
const emit = defineEmits<{ 'update:modelValue': [value: string[]] }>()
const menu = ref(false); const filter = ref('')
const byId = computed(() => new Map(props.options.map((item) => [item.id, { ...item, children: [] as TreeNode[] }])))
const tree = computed<TreeNode[]>(() => { const roots: TreeNode[] = []; for (const node of byId.value.values()) { const parent = node.parentId ? byId.value.get(node.parentId) : undefined; if (parent) parent.children.push(node); else roots.push(node) } return roots })
const summary = computed(() => props.modelValue.length ? `${props.modelValue.length} assunto(s) selecionado(s)` : 'Nenhum assunto selecionado')
function update(value: readonly string[]): void { emit('update:modelValue', [...value]) }
</script>

<style scoped lang="sass">
.subject-tree-select :deep(.q-field__control)
  cursor: pointer
.subject-tree-menu
  width: min(620px, calc(100vw - 24px))
.tree-menu-head
  display: grid
  grid-template-columns: minmax(0, 1fr) auto
  align-items: center
  gap: 12px
.tree-menu-head span
  color: #8ca2c3
  font-size: 12px
.tree-scroll
  max-height: min(520px, 62vh)
  overflow: auto
.tree-node
  display: flex
  align-items: center
  min-height: 38px
  width: 100%
  gap: 10px
  color: #e4efff
.tree-name
  flex: 1
  font-weight: 650
.question-count
  color: #bfe2ff
</style>
