import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'

export type QuestionDifficulty = 'EASY' | 'MEDIUM' | 'HARD'
export interface PublishedQuestionOption {
  readonly id: string
  readonly label: string
  readonly content: string
  readonly position: number
}
export interface PublishedQuestion {
  readonly id: string
  readonly statement: string
  readonly difficulty: QuestionDifficulty
  readonly board: string | null
  readonly year: number | null
  readonly options: readonly PublishedQuestionOption[]
  readonly taxonomySubjectIds?: readonly string[]
  readonly status?: 'DRAFT' | 'REVIEW' | 'PUBLISHED'
  readonly source?: string | null
}
export interface QuestionFilters extends PageQuery {
  readonly subjectId?: string
  readonly board?: string
  readonly year?: number
  readonly difficulty?: QuestionDifficulty
}
export interface QuestionRepository {
  listPublished(filters?: QuestionFilters): Promise<PageResult<PublishedQuestion>>
}
