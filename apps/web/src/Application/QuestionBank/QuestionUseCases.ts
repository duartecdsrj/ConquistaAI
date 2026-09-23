import type { QuestionFilters, PublishedQuestion, QuestionRepository } from '../../Domain/QuestionBank/QuestionRepository'
import type { PageResult } from '../../Infrastructure/Http/AxiosApiClient'

export class ListPublishedQuestionsUseCase {
  public constructor(private readonly repository: QuestionRepository) {}
  public execute(filters?: QuestionFilters): Promise<PageResult<PublishedQuestion>> {
    return this.repository.listPublished(filters)
  }
}
