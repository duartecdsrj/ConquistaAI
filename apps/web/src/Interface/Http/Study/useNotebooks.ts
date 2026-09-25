import { computed, readonly, ref } from 'vue'
import type { CreateNotebookCommand, Notebook, StudyContestSubject, StudyGoal, StudyPlan } from '../../../Domain/Study/StudyRepository'
import type { Exam, Position } from '../../../Domain/Catalog/CatalogRepository'
import { catalogUseCases, studyUseCases } from '../../../Infrastructure/Container'

export function useNotebooks() {
  const loading = ref(false); const saving = ref(false); const planning = ref(false); const goalSaving = ref(false); const error = ref(''); const notebooks = ref<readonly Notebook[]>([]); const total = ref(0); const plan = ref<StudyPlan | null>(null); const goal = ref<StudyGoal | null>(null)
  const contests = ref<readonly Exam[]>([]); const contestSubjects = ref<readonly StudyContestSubject[]>([]); const positions = ref<readonly Position[]>([])
  const notebookFilter = ref<'ALL' | 'ACTIVE' | 'FINISHED'>('ALL')
  const notebookSearch = ref('')
  const visibleNotebooks = computed(() => {
    const term = notebookSearch.value.trim().toLocaleLowerCase('pt-BR')
    return notebooks.value.filter((notebook) => {
      const matchesStatus = notebookFilter.value === 'ALL' || (notebookFilter.value === 'ACTIVE' ? notebook.status !== 'FINISHED' : notebook.status === 'FINISHED')
      return matchesStatus && (!term || notebook.name.toLocaleLowerCase('pt-BR').includes(term))
    })
  })
  const activeCount = computed(() => notebooks.value.filter((notebook) => notebook.status !== 'FINISHED').length)
  const finishedCount = computed(() => notebooks.value.filter((notebook) => notebook.status === 'FINISHED').length)
  async function loadStudyContests(): Promise<void> { try { contests.value = await catalogUseCases.listExams() } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar os concursos.' } }
  async function loadPositionSubjects(positionId: string): Promise<void> { contestSubjects.value = []; if (!positionId) return; try { contestSubjects.value = await studyUseCases.positionSubjects.execute(positionId) } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar os assuntos do cargo.' } }
  async function loadPositions(examId: string): Promise<void> { positions.value = []; if (!examId) return; try { positions.value = await catalogUseCases.listPositions(examId) } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar os cargos.' } }
  async function load(): Promise<void> { loading.value = true; error.value = ''; try { const result = await studyUseCases.list.execute({ page: 1, perPage: 25 }); notebooks.value = result.items; total.value = result.pagination.total } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar seus cadernos.' } finally { loading.value = false } }
  async function loadPlan(): Promise<void> { planning.value = true; try { plan.value = await studyUseCases.plan.execute() } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar seu plano de estudos.' } finally { planning.value = false } }
  async function loadGoal(): Promise<void> { try { goal.value = await studyUseCases.goal.execute() } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar sua meta semanal.' } }
  async function updateGoal(weeklyQuestionGoal: number): Promise<boolean> { goalSaving.value = true; error.value = ''; try { goal.value = await studyUseCases.updateGoal.execute(weeklyQuestionGoal); return true } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível salvar sua meta semanal.'; return false } finally { goalSaving.value = false } }
  async function create(command: CreateNotebookCommand): Promise<Notebook | null> { saving.value = true; error.value = ''; try { const notebook = await studyUseCases.create.execute(command); notebooks.value = [notebook, ...notebooks.value]; total.value += 1; return notebook } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível criar o caderno.'; return null } finally { saving.value = false } }
  function selectNotebookFilter(filter: 'ALL' | 'ACTIVE' | 'FINISHED'): void { notebookFilter.value = filter }
  return { activeCount, contests: readonly(contests), positions: readonly(positions), contestSubjects: readonly(contestSubjects), loadStudyContests, loadPositionSubjects, loadPositions, create, error: readonly(error), goal: readonly(goal), goalSaving: readonly(goalSaving), load, loadGoal, loadPlan, loading: readonly(loading), notebooks: readonly(notebooks), plan: readonly(plan), planning: readonly(planning), saving: readonly(saving), notebookFilter: readonly(notebookFilter), notebookSearch, selectNotebookFilter, total: readonly(total), updateGoal, visibleNotebooks, finishedCount }
}
