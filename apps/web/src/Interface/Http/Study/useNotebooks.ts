import { readonly, ref } from 'vue'
import type { CreateNotebookCommand, Notebook, StudyGoal, StudyPlan } from '../../../Domain/Study/StudyRepository'
import { studyUseCases } from '../../../Infrastructure/Container'

export function useNotebooks() {
  const loading = ref(false); const saving = ref(false); const planning = ref(false); const goalSaving = ref(false); const error = ref(''); const notebooks = ref<readonly Notebook[]>([]); const total = ref(0); const plan = ref<StudyPlan | null>(null); const goal = ref<StudyGoal | null>(null)
  async function load(): Promise<void> { loading.value = true; error.value = ''; try { const result = await studyUseCases.list.execute({ page: 1, perPage: 25 }); notebooks.value = result.items; total.value = result.pagination.total } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar seus cadernos.' } finally { loading.value = false } }
  async function loadPlan(): Promise<void> { planning.value = true; try { plan.value = await studyUseCases.plan.execute() } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar seu plano de estudos.' } finally { planning.value = false } }
  async function loadGoal(): Promise<void> { try { goal.value = await studyUseCases.goal.execute() } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar sua meta semanal.' } }
  async function updateGoal(weeklyQuestionGoal: number): Promise<boolean> { goalSaving.value = true; error.value = ''; try { goal.value = await studyUseCases.updateGoal.execute(weeklyQuestionGoal); return true } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível salvar sua meta semanal.'; return false } finally { goalSaving.value = false } }
  async function create(command: CreateNotebookCommand): Promise<Notebook | null> { saving.value = true; error.value = ''; try { const notebook = await studyUseCases.create.execute(command); notebooks.value = [notebook, ...notebooks.value]; total.value += 1; return notebook } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível criar o caderno.'; return null } finally { saving.value = false } }
  return { create, error: readonly(error), goal: readonly(goal), goalSaving: readonly(goalSaving), load, loadGoal, loadPlan, loading: readonly(loading), notebooks: readonly(notebooks), plan: readonly(plan), planning: readonly(planning), saving: readonly(saving), total: readonly(total), updateGoal }
}
