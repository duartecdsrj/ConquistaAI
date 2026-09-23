import { readonly, ref } from 'vue'
import type { Exam, Position, Subject, Syllabus, Tag } from '../../../Domain/Catalog/CatalogRepository'
import { catalogUseCases } from '../../../Infrastructure/Container'
export function useCatalog() {
  const loading = ref(false); const saving = ref(false); const error = ref('')
  const exams = ref<readonly Exam[]>([]); const positions = ref<readonly Position[]>([]); const syllabi = ref<readonly Syllabus[]>([]); const subjects = ref<readonly Subject[]>([]); const tags = ref<readonly Tag[]>([])
  async function run(action: () => Promise<void>): Promise<void> { loading.value = true; error.value = ''; try { await action() } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar o catálogo.' } finally { loading.value = false } }
  async function loadExams(): Promise<void> { await run(async () => { exams.value = await catalogUseCases.listExams(); tags.value = await catalogUseCases.listTags() }) }
  async function chooseExam(id: string): Promise<void> { await run(async () => { positions.value = await catalogUseCases.listPositions(id); syllabi.value = []; subjects.value = [] }) }
  async function choosePosition(id: string): Promise<void> { await run(async () => { syllabi.value = await catalogUseCases.listSyllabi(id); subjects.value = [] }) }
  async function chooseSyllabus(id: string): Promise<void> { await run(async () => { subjects.value = await catalogUseCases.listSubjects(id) }) }
  async function save(action: () => Promise<void>): Promise<void> { saving.value = true; error.value = ''; try { await action() } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível salvar.' } finally { saving.value = false } }
  return { loading: readonly(loading), saving: readonly(saving), error: readonly(error), exams: readonly(exams), positions: readonly(positions), syllabi: readonly(syllabi), subjects: readonly(subjects), tags: readonly(tags), loadExams, chooseExam, choosePosition, chooseSyllabus, save }
}
