import type { EditorialRepository } from '../../Domain/Editorial/EditorialRepository'
import type { PageQuery, PageResult } from '../../Infrastructure/Http/AxiosApiClient'
import type { PublishedQuestion } from '../../Domain/QuestionBank/QuestionRepository'
export class ListDraftQuestionsUseCase { public constructor(private readonly repository:EditorialRepository){} public execute(query?:PageQuery):Promise<PageResult<PublishedQuestion>> { return this.repository.listDrafts(query) } }
export class PublishEditorialQuestionUseCase { public constructor(private readonly repository:EditorialRepository){} public execute(questionId:string):Promise<void> { return this.repository.publish(questionId) } }
export class AssignEditorialQuestionTaxonomyUseCase { public constructor(private readonly repository:EditorialRepository){} public execute(questionId:string, subjectIds:readonly string[]):Promise<void>{return this.repository.assignTaxonomy(questionId,subjectIds)} }

export class MarkEditorialQuestionsForApprovalUseCase { public constructor(private readonly repository: EditorialRepository) {} public execute(questionIds: readonly string[]): Promise<number> { if (!questionIds.length) return Promise.reject(new Error('Selecione ao menos uma questão.')); return this.repository.markForApproval(questionIds) } }
