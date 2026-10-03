import { readonly, ref } from 'vue'
import type { BasicStatistics, SyllabusDashboard, StudyMap, SaveStudyMapSchedule } from '../../../Domain/Performance/PerformanceRepository'
import { performanceUseCases } from '../../../Infrastructure/Container'

export function usePerformance() {
  const loading = ref(false)
  const error = ref('')
  const statistics = ref<BasicStatistics | null>(null)
  const dashboard = ref<SyllabusDashboard | null>(null)
  const studyMap = ref<StudyMap | null>(null)
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
  async function loadStudyMap(examId:string, from?:string, to?:string):Promise<void>{ loading.value=true; error.value=""; try { studyMap.value=await performanceUseCases.studyMap.execute(examId,from,to) } catch(reason){ error.value=reason instanceof Error?reason.message:"Não foi possível carregar o mapa de estudo." } finally { loading.value=false } }
  async function saveStudyMapSchedule(input:SaveStudyMapSchedule):Promise<boolean>{ loading.value=true; error.value=""; try { await performanceUseCases.saveStudyMapSchedule.execute(input); await loadStudyMap(input.examId); return true } catch(reason){ error.value=reason instanceof Error?reason.message:"Não foi possível salvar o cronograma."; return false } finally { loading.value=false } }
  return { loading: readonly(loading), error: readonly(error), statistics: readonly(statistics), dashboard: readonly(dashboard), studyMap: readonly(studyMap), load, loadDashboard, loadStudyMap, saveStudyMapSchedule }
}
