import type { PublishedQuestion } from '../../Domain/QuestionBank/QuestionRepository'
import type { DirectedStudyPlan, CreateNotebookCommand, Notebook, NotebookStatistics, NotebookStatus, StudyContestSubject, StudyGoal, StudyPlan, StudyRepository } from '../../Domain/Study/StudyRepository'
import { getData, getPage, postData, putData, type PageQuery, type PageResult } from '../Http/AxiosApiClient'

interface NotebookApi {
  readonly id: string
  readonly name: string
  readonly mode: 'STUDY' | 'EXAM'
  readonly questionIds: readonly string[]
  readonly createdAt: string
  readonly status: NotebookStatus
  readonly startedAt: string | null
  readonly finishedAt: string | null
  readonly filters: Readonly<Record<string, unknown>>
  readonly durationSeconds: number | null
}

const mapNotebook = (value: NotebookApi): Notebook => ({ ...value })

export class AxiosStudyRepository implements StudyRepository {
  public positionSubjects(positionId: string): Promise<readonly StudyContestSubject[]> { return getData('/study-positions/' + encodeURIComponent(positionId) + '/subjects') }
  public directedPlans(): Promise<readonly DirectedStudyPlan[]> { return getData('/directed-study-plans') }
  public createDirectedPlan(command: { name:string; examId:string; positionId:string }): Promise<DirectedStudyPlan> { return postData('/directed-study-plans', { name: command.name, exam_id: command.examId, position_id: command.positionId }) }
  public async list(query?: PageQuery): Promise<PageResult<Notebook>> { const page = await getPage<NotebookApi>('/notebooks', query); return { ...page, items: page.items.map(mapNotebook) } }
  public async get(id: string): Promise<Notebook> { return mapNotebook(await getData<NotebookApi>('/notebooks/' + encodeURIComponent(id))) }
  public listQuestions(id: string, query?: PageQuery): Promise<PageResult<PublishedQuestion>> { return getPage<PublishedQuestion>('/notebooks/' + encodeURIComponent(id) + '/questions', query) }
  public async create(command: CreateNotebookCommand): Promise<Notebook> { return mapNotebook(await postData<NotebookApi, Record<string, unknown>>('/notebooks', { name: command.name, mode: command.mode, quantity: command.quantity, filters: { ...(command.filters.examId ? { exam_id: command.filters.examId } : {}), ...(command.filters.positionId ? { position_id: command.filters.positionId } : {}), ...(command.filters.subjectIds?.length ? { subject_ids: command.filters.subjectIds } : {}), ...(command.filters.subjectId ? { subject_id: command.filters.subjectId } : {}), ...(command.filters.board ? { board: command.filters.board } : {}), ...(command.filters.year ? { year: command.filters.year } : {}), ...(command.filters.difficulty ? { difficulty: command.filters.difficulty } : {}) } })) }
  public async start(id: string): Promise<Notebook> { return mapNotebook(await postData<NotebookApi, undefined>('/notebooks/' + encodeURIComponent(id) + '/start')) }
  public async pause(id: string): Promise<Notebook> { return mapNotebook(await postData<NotebookApi, undefined>('/notebooks/' + encodeURIComponent(id) + '/pause')) }
  public statistics(id: string): Promise<NotebookStatistics> { return getData<NotebookStatistics>('/notebooks/' + encodeURIComponent(id) + '/statistics') }
  public async finish(id: string): Promise<Notebook> { return mapNotebook(await postData<NotebookApi, undefined>('/notebooks/' + encodeURIComponent(id) + '/finish')) }
  public plan(): Promise<StudyPlan> { return getData<StudyPlan>('/study-plan/me') }
  public getGoal(): Promise<StudyGoal> { return getData<StudyGoal>('/study-goals/me') }
  public updateGoal(weeklyQuestionGoal: number): Promise<StudyGoal> { return putData<StudyGoal, { weekly_question_goal: number }>('/study-goals/me', { weekly_question_goal: weeklyQuestionGoal }) }
}
