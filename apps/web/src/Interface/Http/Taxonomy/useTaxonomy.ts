import { computed, readonly, ref } from 'vue'
import type { TaxonomySubject } from '../../../Domain/Taxonomy/TaxonomyRepository'
import { taxonomyUseCases } from '../../../Infrastructure/Container'

export interface TaxonomyTreeNode {
  readonly id: string
  readonly label: string
  children: TaxonomyTreeNode[]
}

export function useTaxonomy() {
  const loading = ref(false)
  const saving = ref(false)
  const error = ref('')
  const subjects = ref<readonly TaxonomySubject[]>([])

  const tree = computed<TaxonomyTreeNode[]>(() => {
    const nodes = new Map(subjects.value.map((subject) => [subject.id, { id: subject.id, label: subject.name, children: [] as TaxonomyTreeNode[] }]))
    const roots: TaxonomyTreeNode[] = []
    for (const subject of subjects.value) {
      const node = nodes.get(subject.id)!
      const parent = subject.parentId ? nodes.get(subject.parentId) : undefined
      parent ? parent.children.push(node) : roots.push(node)
    }
    return roots
  })

  async function load(): Promise<void> {
    loading.value = true
    error.value = ''
    try { subjects.value = (await taxonomyUseCases.list.execute({ page: 1, perPage: 100 })).items }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar a taxonomia.' }
    finally { loading.value = false }
  }

  async function createAlias(subjectId: string, alias: string): Promise<boolean> {
    saving.value = true
    error.value = ''
    try { await taxonomyUseCases.createAlias.execute({ subjectId, alias }); return true }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível criar o alias.'; return false }
    finally { saving.value = false }
  }

  async function create(name: string, parentId: string | null, description: string | null): Promise<boolean> {
    saving.value = true
    error.value = ''
    try {
      subjects.value = [...subjects.value, await taxonomyUseCases.create.execute({ name, parentId, description })]
      return true
    } catch (reason) {
      error.value = reason instanceof Error ? reason.message : 'Não foi possível criar o assunto.'
      return false
    } finally { saving.value = false }
  }

  return { create, createAlias, error: readonly(error), load, loading: readonly(loading), saving: readonly(saving), subjects: readonly(subjects), tree }
}
