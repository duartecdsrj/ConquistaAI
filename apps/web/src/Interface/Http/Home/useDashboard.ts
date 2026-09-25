import { computed, readonly, ref } from 'vue'
import type { BasicStatistics } from '../../../Domain/Performance/PerformanceRepository'
import type { Notebook } from '../../../Domain/Study/StudyRepository'
import { performanceUseCases, studyUseCases } from '../../../Infrastructure/Container'

export function useDashboard() {
  const loading = ref(true)
  const error = ref('')
  const statistics = ref<BasicStatistics | null>(null)
  const notebooks = ref<readonly Notebook[]>([])
  const notebookTotal = ref(0)
  const hasData = computed(() => statistics.value !== null)
  async function load(): Promise<void> {
    loading.value = true; error.value = ''
    const [statisticsResult, notebooksResult] = await Promise.allSettled([performanceUseCases.getMine.execute(), studyUseCases.list.execute({ page: 1, perPage: 3 })])
    if (statisticsResult.status === 'fulfilled') statistics.value = statisticsResult.value
    if (notebooksResult.status === 'fulfilled') { notebooks.value = notebooksResult.value.items; notebookTotal.value = notebooksResult.value.pagination.total }
    if (statisticsResult.status === 'rejected' && notebooksResult.status === 'rejected') error.value = 'Não foi possível atualizar seus dados agora.'
    loading.value = false
  }
  return { loading: readonly(loading), error: readonly(error), statistics: readonly(statistics), notebooks: readonly(notebooks), notebookTotal: readonly(notebookTotal), hasData, load }
}
