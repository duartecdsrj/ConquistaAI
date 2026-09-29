import { readonly, ref, watch, type Ref } from 'vue'
import type { QuestionInteraction, UpdateQuestionInteractionCommand } from '../../../Domain/QuestionLearning/QuestionLearningRepository'
import { questionLearningUseCases } from '../../../Infrastructure/Container'

export function useQuestionInteractions(questionId: Ref<string>) {
  const interaction = ref<QuestionInteraction | null>(null)
  const loading = ref(false)
  const saving = ref(false)
  const error = ref('')
  async function load(): Promise<void> {
    loading.value = true
    error.value = ''
    try { interaction.value = await questionLearningUseCases.interaction.execute(questionId.value) }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar suas marcações.' }
    finally { loading.value = false }
  }
  async function update(command: UpdateQuestionInteractionCommand): Promise<boolean> {
    if (!interaction.value || saving.value) return false
    const previous = interaction.value
    interaction.value = { ...previous, ...command }
    saving.value = true
    error.value = ''
    try { interaction.value = await questionLearningUseCases.updateInteraction.execute(questionId.value, command); return true }
    catch (reason) { interaction.value = previous; error.value = reason instanceof Error ? reason.message : 'Não foi possível salvar sua marcação.'; return false }
    finally { saving.value = false }
  }
  watch(questionId, () => { interaction.value = null; void load() }, { immediate: true })
  return { error: readonly(error), interaction: readonly(interaction), loading: readonly(loading), saving: readonly(saving), update }
}
