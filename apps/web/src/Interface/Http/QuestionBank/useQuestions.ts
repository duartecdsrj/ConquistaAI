import { readonly, ref } from 'vue'
import type { QuestionFilters, PublishedQuestion } from '../../../Domain/QuestionBank/QuestionRepository'
import { questionUseCases } from '../../../Infrastructure/Container'
export function useQuestions() {
  const loading = ref(false); const error = ref(''); const questions = ref<readonly PublishedQuestion[]>([]); const total = ref(0)
  async function load(filters: QuestionFilters = {}): Promise<void> {
    loading.value = true; error.value = ''
    try { const result = await questionUseCases.listPublished.execute({ page: 1, perPage: 25, ...filters }); questions.value = result.items; total.value = result.pagination.total }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar as questões.' }
    finally { loading.value = false }
  }
  return { loading: readonly(loading), error: readonly(error), questions: readonly(questions), total: readonly(total), load }
}
