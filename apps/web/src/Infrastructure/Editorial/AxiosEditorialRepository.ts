import type { EditorialRepository } from '../../Domain/Editorial/EditorialRepository'
import type { PublishedQuestion } from '../../Domain/QuestionBank/QuestionRepository'
import { getPage, postData, putData, type PageQuery, type PageResult } from '../Http/AxiosApiClient'
export class AxiosEditorialRepository implements EditorialRepository { public listDrafts(query?:PageQuery):Promise<PageResult<PublishedQuestion>> { return getPage<PublishedQuestion>('/admin/questions/drafts',query) } public async assignTaxonomy(questionId:string,subjectIds:readonly string[]):Promise<void>{await putData(`/admin/questions/${questionId}/taxonomy-subjects`,{taxonomy_subject_ids:subjectIds})} public async publish(questionId:string):Promise<void>{ await postData(`/admin/questions/${questionId}/publish`) } }
