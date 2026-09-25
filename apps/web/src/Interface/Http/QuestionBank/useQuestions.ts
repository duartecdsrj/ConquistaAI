import { readonly, ref } from 'vue'
import type { QuestionFilters, PublishedQuestion } from '../../../Domain/QuestionBank/QuestionRepository'
import { questionUseCases } from '../../../Infrastructure/Container'
export function useQuestions() {
  const loading = ref(false); const error = ref(''); const questions = ref<readonly PublishedQuestion[]>([]); const total = ref(0); const page = ref(1); const perPage = 25; const filters = ref<QuestionFilters>({})
  async function load(nextFilters: QuestionFilters = {}, nextPage = 1): Promise<void> {
    loading.value = true; error.value = ''; filters.value = nextFilters; page.value = nextPage
    try { const result = await questionUseCases.listPublished.execute({ page: page.value, perPage, ...filters.value }); questions.value = result.items; total.value = result.pagination.total }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar as questões.' }
    finally { loading.value = false }
  }
  async function goTo(nextPage: number): Promise<void> { await load(filters.value, nextPage) }
  return { loading: readonly(loading), error: readonly(error), questions: readonly(questions), total: readonly(total), page: readonly(page), perPage, load, goTo }
}
