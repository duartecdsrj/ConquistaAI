import type { QuestionCorrectionRequest, QuestionPdfAssistance, PublishedQuestion, QuestionFilters, QuestionRepository } from '../../Domain/QuestionBank/QuestionRepository'
import { getData, getPage, postData, type PageResult } from '../Http/AxiosApiClient'

interface QuestionApi {
  readonly id: string
  readonly statement: string
  readonly difficulty: 'EASY' | 'MEDIUM' | 'HARD'
  readonly board: string | null
  readonly year: number | null
  readonly options: readonly { readonly id: string; readonly label: string; readonly content: string; readonly position: number }[]
  readonly answerKeySource?: 'OFFICIAL' | 'AI_ESTIMATED' | null
}
export class AxiosQuestionRepository implements QuestionRepository {
  public askPdfAssistance(questionId:string, question:string): Promise<QuestionPdfAssistance> { return postData('/questions/'+encodeURIComponent(questionId)+'/pdf-assistance',{question}) }
  public requestCorrection(questionId:string,instruction:string):Promise<QuestionCorrectionRequest>{return postData('/questions/'+encodeURIComponent(questionId)+'/correction-requests',{instruction})}
  public latestCorrection(questionId:string):Promise<QuestionCorrectionRequest>{return getData('/questions/'+encodeURIComponent(questionId)+'/correction-requests/latest')}
  public approveCorrection(id:string):Promise<QuestionCorrectionRequest>{return postData('/admin/question-correction-requests/'+encodeURIComponent(id)+'/approve',undefined)}
  public async listPublished(filters: QuestionFilters = {}): Promise<PageResult<PublishedQuestion>> {
    const page = await getPage<QuestionApi>('/questions', { page: filters.page, perPage: filters.perPage }, {
      ...(filters.subjectId ? { subject_id: filters.subjectId } : {}),
      ...(filters.board ? { board: filters.board } : {}),
      ...(filters.year ? { year: filters.year } : {}),
      ...(filters.difficulty ? { difficulty: filters.difficulty } : {}),
      ...(filters.content ? { content: filters.content } : {}),
    })
    return { ...page, items: page.items }
  }
}
