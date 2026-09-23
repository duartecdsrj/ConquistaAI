import { readonly, ref } from 'vue'
import type { CreateNotebookCommand, Notebook } from '../../../Domain/Study/StudyRepository'
import { studyUseCases } from '../../../Infrastructure/Container'

export function useNotebooks() {
  const loading = ref(false)
  const saving = ref(false)
  const error = ref('')
  const notebooks = ref<readonly Notebook[]>([])
  const total = ref(0)

  async function load(): Promise<void> {
    loading.value = true
    error.value = ''
    try {
      const result = await studyUseCases.list.execute({ page: 1, perPage: 25 })
      notebooks.value = result.items
      total.value = result.pagination.total
    } catch (reason) {
      error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar seus cadernos.'
    } finally {
      loading.value = false
    }
  }

  async function create(command: CreateNotebookCommand): Promise<Notebook | null> {
    saving.value = true
    error.value = ''
    try {
      const notebook = await studyUseCases.create.execute(command)
      notebooks.value = [notebook, ...notebooks.value]
      total.value += 1
      return notebook
    } catch (reason) {
      error.value = reason instanceof Error ? reason.message : 'Não foi possível criar o caderno.'
      return null
    } finally {
      saving.value = false
    }
  }

  return { create, error: readonly(error), load, loading: readonly(loading), notebooks: readonly(notebooks), saving: readonly(saving), total: readonly(total) }
}
