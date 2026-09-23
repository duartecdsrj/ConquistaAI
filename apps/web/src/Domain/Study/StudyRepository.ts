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
  readonly durationSeconds: number | null
}

export interface NotebookStatistics { readonly total: number; readonly answered: number; readonly correct: number; readonly incorrect: number; readonly percentage: number; readonly averageElapsedSeconds: number; readonly elapsedSeconds: number; readonly answeredQuestionIds: readonly string[] }

export interface CreateNotebookCommand {
  readonly name: string
  readonly mode: NotebookMode
  readonly quantity: number
  readonly filters: Readonly<{ subjectId?: string; board?: string; year?: number; difficulty?: 'EASY' | 'MEDIUM' | 'HARD' }>
}

export interface StudyRepository {
  list(query?: PageQuery): Promise<PageResult<Notebook>>
  get(id: string): Promise<Notebook>
  listQuestions(id: string, query?: PageQuery): Promise<PageResult<PublishedQuestion>>
  create(command: CreateNotebookCommand): Promise<Notebook>
  start(id: string): Promise<Notebook>
  pause(id: string): Promise<Notebook>
  statistics(id: string): Promise<NotebookStatistics>
  finish(id: string): Promise<Notebook>
}
