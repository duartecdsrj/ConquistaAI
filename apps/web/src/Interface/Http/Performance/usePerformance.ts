import { readonly, ref } from 'vue'
import type { BasicStatistics, SyllabusDashboard } from '../../../Domain/Performance/PerformanceRepository'
import { performanceUseCases } from '../../../Infrastructure/Container'

export function usePerformance() {
  const loading = ref(false)
  const error = ref('')
  const statistics = ref<BasicStatistics | null>(null)
  const dashboard = ref<SyllabusDashboard | null>(null)
  async function load(): Promise<void> {
    loading.value = true; error.value = ''
    try { statistics.value = await performanceUseCases.getMine.execute() }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar seu desempenho.' }
    finally { loading.value = false }
  }
  async function loadDashboard(syllabusId?: string, examId?: string): Promise<void> {
    loading.value = true; error.value = ''
    try { dashboard.value = await performanceUseCases.getDashboard.execute(syllabusId, examId) }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar o painel por edital.' }
    finally { loading.value = false }
  }
  return { loading: readonly(loading), error: readonly(error), statistics: readonly(statistics), dashboard: readonly(dashboard), load, loadDashboard }
}
