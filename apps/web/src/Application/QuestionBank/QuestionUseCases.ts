import type { QuestionPdfAssistance, QuestionFilters, PublishedQuestion, QuestionRepository } from '../../Domain/QuestionBank/QuestionRepository'
import type { PageResult } from '../../Infrastructure/Http/AxiosApiClient'

export class ListPublishedQuestionsUseCase {
  public constructor(private readonly repository: QuestionRepository) {}
  public execute(filters?: QuestionFilters): Promise<PageResult<PublishedQuestion>> {
    return this.repository.listPublished(filters)
  }
}

export class AskQuestionPdfAssistanceUseCase { public constructor(private readonly repository: QuestionRepository) {} public execute(questionId:string, question:string): Promise<QuestionPdfAssistance> { if(question.trim().length<3)return Promise.reject(new Error('Descreva sua dúvida.'));return this.repository.askPdfAssistance(questionId,question.trim()) } }
