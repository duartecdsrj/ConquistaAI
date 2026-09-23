import type { PublishedQuestion } from '../QuestionBank/QuestionRepository'
import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'

export type NotebookMode = 'STUDY' | 'EXAM'
export interface Notebook { readonly id: string; readonly name: string; readonly mode: NotebookMode; readonly questionIds: readonly string[]; readonly createdAt: string }
export interface CreateNotebookCommand { readonly name: string; readonly mode: NotebookMode; readonly quantity: number; readonly filters: Readonly<{ subjectId?: string; board?: string; year?: number; difficulty?: 'EASY' | 'MEDIUM' | 'HARD' }> }
export interface StudyRepository {
  list(query?: PageQuery): Promise<PageResult<Notebook>>
  get(id: string): Promise<Notebook>
  listQuestions(id: string, query?: PageQuery): Promise<PageResult<PublishedQuestion>>
  create(command: CreateNotebookCommand): Promise<Notebook>
}
