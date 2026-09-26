import type { PublishedQuestion } from '../QuestionBank/QuestionRepository'
import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'

export type NotebookMode = 'STUDY' | 'EXAM'
export type NotebookStatus = 'DRAFT' | 'IN_PROGRESS' | 'PAUSED' | 'FINISHED'

export interface Notebook {
  readonly id: string
  readonly name: string
  readonly mode: NotebookMode
  readonly questionIds: readonly string[]
  readonly createdAt: string
  readonly status: NotebookStatus
  readonly startedAt: string | null
  readonly finishedAt: string | null
  readonly filters: Readonly<Record<string, unknown>>
  readonly durationSeconds: number | null
}

export interface NotebookStatistics { readonly total: number; readonly answered: number; readonly correct: number; readonly incorrect: number; readonly percentage: number; readonly averageElapsedSeconds: number; readonly elapsedSeconds: number; readonly answeredQuestionIds: readonly string[] }

export interface StudyContestSubject { readonly id:string; readonly parentId:string|null; readonly name:string; readonly level:number; readonly selectionWeight:number }
export interface DirectedStudyPlan { readonly id:string; readonly examId:string; readonly positionId:string; readonly name:string; readonly createdAt:string }
export interface StudyGoal { readonly weeklyQuestionGoal: number; readonly completedQuestions: number; readonly percentage: number; readonly periodStartsAt: string }
export interface StudyPlanPriority { readonly subjectId: string; readonly total: number; readonly correct: number; readonly percentage: number; readonly distinctDays: number; readonly sufficientData: boolean; readonly reason: string; readonly action: 'RESPONDER_CONJUNTO_FILTRADO' | 'PRATICAR_AMOSTRA' }
export interface StudyPlan { readonly priorities: readonly StudyPlanPriority[] }
export interface CreateNotebookCommand {
  readonly name: string
  readonly mode: NotebookMode
  readonly quantity: number
  readonly filters: Readonly<{ examId?: string; positionId?: string; subjectIds?: readonly string[]; subjectId?: string; syllabusId?: string; board?: string; year?: number; difficulty?: 'EASY' | 'MEDIUM' | 'HARD' }>
}

export interface StudyRepository {
  positionSubjects(positionId: string): Promise<readonly StudyContestSubject[]>
  directedPlans(): Promise<readonly DirectedStudyPlan[]>
  createDirectedPlan(command: { name:string; examId:string; positionId:string }): Promise<DirectedStudyPlan>
  list(query?: PageQuery): Promise<PageResult<Notebook>>
  get(id: string): Promise<Notebook>
  listQuestions(id: string, query?: PageQuery): Promise<PageResult<PublishedQuestion>>
  create(command: CreateNotebookCommand): Promise<Notebook>
  getGoal(): Promise<StudyGoal>
  updateGoal(weeklyQuestionGoal: number): Promise<StudyGoal>
  plan(): Promise<StudyPlan>
  start(id: string): Promise<Notebook>
  pause(id: string): Promise<Notebook>
  statistics(id: string): Promise<NotebookStatistics>
  finish(id: string): Promise<Notebook>
}
