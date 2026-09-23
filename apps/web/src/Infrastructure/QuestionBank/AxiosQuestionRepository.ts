import type { PublishedQuestion, QuestionFilters, QuestionRepository } from '../../Domain/QuestionBank/QuestionRepository'
import { getPage, type PageResult } from '../Http/AxiosApiClient'

interface QuestionApi {
  readonly id: string
  readonly statement: string
  readonly difficulty: 'EASY' | 'MEDIUM' | 'HARD'
  readonly board: string | null
  readonly year: number | null
  readonly options: readonly { readonly id: string; readonly label: string; readonly content: string; readonly position: number }[]
}
export class AxiosQuestionRepository implements QuestionRepository {
  public async listPublished(filters: QuestionFilters = {}): Promise<PageResult<PublishedQuestion>> {
    const page = await getPage<QuestionApi>('/questions', { page: filters.page, perPage: filters.perPage }, {
      ...(filters.subjectId ? { subject_id: filters.subjectId } : {}),
      ...(filters.board ? { board: filters.board } : {}),
      ...(filters.year ? { year: filters.year } : {}),
      ...(filters.difficulty ? { difficulty: filters.difficulty } : {}),
    })
    return { ...page, items: page.items }
  }
}
