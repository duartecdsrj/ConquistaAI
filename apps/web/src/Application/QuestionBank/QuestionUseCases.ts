import type { QuestionPdfAssistance, QuestionFilters, PublishedQuestion, QuestionRepository } from '../../Domain/QuestionBank/QuestionRepository'
import type { PageResult } from '../../Infrastructure/Http/AxiosApiClient'

export class ListPublishedQuestionsUseCase {
  public constructor(private readonly repository: QuestionRepository) {}
  public execute(filters?: QuestionFilters): Promise<PageResult<PublishedQuestion>> {
    return this.repository.listPublished(filters)
  }
}

export class AskQuestionPdfAssistanceUseCase { public constructor(private readonly repository: QuestionRepository) {} public execute(questionId:string, question:string): Promise<QuestionPdfAssistance> { if(question.trim().length<3)return Promise.reject(new Error('Descreva sua dúvida.'));return this.repository.askPdfAssistance(questionId,question.trim()) } }

export class RequestQuestionCorrectionUseCase { public constructor(private readonly repository:QuestionRepository){} public execute(questionId:string,instruction:string):Promise<import('../../Domain/QuestionBank/QuestionRepository').QuestionCorrectionRequest>{if(instruction.trim().length<3)return Promise.reject(new Error('Descreva a correção.'));return this.repository.requestCorrection(questionId,instruction.trim())} }
export class GetLatestQuestionCorrectionUseCase { public constructor(private readonly repository:QuestionRepository){} public execute(questionId:string):Promise<import('../../Domain/QuestionBank/QuestionRepository').QuestionCorrectionRequest>{return this.repository.latestCorrection(questionId)} }
export class GetLatestCompletedQuestionCorrectionUseCase { public constructor(private readonly repository:QuestionRepository){} public execute():Promise<import('../../Domain/QuestionBank/QuestionRepository').QuestionCorrectionRequest>{return this.repository.latestCompletedCorrection()} }
export class ApproveQuestionCorrectionUseCase { public constructor(private readonly repository:QuestionRepository){} public execute(id:string):Promise<import('../../Domain/QuestionBank/QuestionRepository').QuestionCorrectionRequest>{return this.repository.approveCorrection(id)} }
