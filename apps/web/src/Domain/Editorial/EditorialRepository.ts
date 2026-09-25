import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'
import type { PublishedQuestion } from '../QuestionBank/QuestionRepository'
export interface EditorialRepository { listDrafts(query?:PageQuery):Promise<PageResult<PublishedQuestion>>; publish(questionId:string):Promise<void>; assignTaxonomy(questionId:string, subjectIds:readonly string[]):Promise<void>; markForApproval(questionIds:readonly string[]):Promise<number> }
