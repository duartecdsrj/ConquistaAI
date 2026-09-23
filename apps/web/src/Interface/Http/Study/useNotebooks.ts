import { readonly, ref } from 'vue'
import type { PublishedQuestion } from '../../../Domain/QuestionBank/QuestionRepository'
import type { CreateNotebookCommand, Notebook } from '../../../Domain/Study/StudyRepository'
import { performanceUseCases, studyUseCases } from '../../../Infrastructure/Container'
export function useNotebooks() {
  const loading = ref(false); const saving = ref(false); const loadingQuestions = ref(false); const submittingAnswer = ref(false); const error = ref(''); const answerMessage = ref(''); const notebooks = ref<readonly Notebook[]>([]); const total = ref(0); const questions = ref<readonly PublishedQuestion[]>([])
  async function load(): Promise<void> {
    loading.value = true; error.value = ''
    try { const result = await studyUseCases.list.execute({ page: 1, perPage: 25 }); notebooks.value = result.items; total.value = result.pagination.total }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar seus cadernos.' } finally { loading.value = false }
  }
  async function loadQuestions(notebookId: string): Promise<void> {
    loadingQuestions.value = true; error.value = ''; answerMessage.value = ''; questions.value = []
    try { questions.value = (await studyUseCases.listQuestions.execute(notebookId, { page: 1, perPage: 100 })).items }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar as questões deste caderno.' } finally { loadingQuestions.value = false }
  }
  async function submitAnswer(notebookId: string, questionId: string, optionId: string): Promise<boolean> {
    submittingAnswer.value = true; error.value = ''; answerMessage.value = ''
    try { await performanceUseCases.submitAnswer.execute(notebookId, questionId, optionId, 0); answerMessage.value = 'Resposta registrada e tentativa concluída.'; return true }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível registrar a resposta.'; return false } finally { submittingAnswer.value = false }
  }
  async function create(command: CreateNotebookCommand): Promise<Notebook | null> {
    saving.value = true; error.value = ''
    try { const notebook = await studyUseCases.create.execute(command); notebooks.value = [notebook, ...notebooks.value]; total.value += 1; return notebook }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível criar o caderno.'; return null } finally { saving.value = false }
  }
  return { loading: readonly(loading), saving: readonly(saving), loadingQuestions: readonly(loadingQuestions), submittingAnswer: readonly(submittingAnswer), error: readonly(error), answerMessage: readonly(answerMessage), notebooks: readonly(notebooks), total: readonly(total), questions: readonly(questions), load, loadQuestions, submitAnswer, create }
}
