import type { CreateQuestionCommentCommand, CreateQuestionProblemReportCommand, QuestionComment, QuestionExplanation, QuestionInteraction, QuestionLearningRepository, QuestionNote, QuestionProblemReport, UpdateQuestionInteractionCommand } from '../../Domain/QuestionLearning/QuestionLearningRepository'
import { getData, getPage, patchData, postData, putData } from '../Http/AxiosApiClient'

const path = (questionId: string): string => '/questions/' + encodeURIComponent(questionId)
export class AxiosQuestionLearningRepository implements QuestionLearningRepository {
  public getInteraction(questionId: string): Promise<QuestionInteraction> { return getData(path(questionId) + '/learning-interactions') }
  public updateInteraction(questionId: string, command: UpdateQuestionInteractionCommand): Promise<QuestionInteraction> { return patchData(path(questionId) + '/learning-interactions', { ...(command.favorite !== undefined ? { favorite: command.favorite } : {}), ...(command.reviewLater !== undefined ? { review_later: command.reviewLater } : {}), ...(command.notMastered !== undefined ? { not_mastered: command.notMastered } : {}) }) }
  public getNote(questionId: string): Promise<QuestionNote | null> { return getData(path(questionId) + '/note') }
  public saveNote(questionId: string, content: string): Promise<QuestionNote> { return putData(path(questionId) + '/note', { content }) }
  public async listComments(questionId: string, page?: number, perPage?: number): Promise<readonly QuestionComment[]> { return (await getPage<QuestionComment>(path(questionId) + '/comments', { page, perPage })).items }
  public createComment(questionId: string, command: CreateQuestionCommentCommand): Promise<QuestionComment> { return postData(path(questionId) + '/comments', { content: command.content, ...(command.parentId ? { parent_id: command.parentId } : {}) }) }
  public createProblemReport(questionId: string, command: CreateQuestionProblemReportCommand): Promise<QuestionProblemReport> { return postData(path(questionId) + '/problem-reports', { category: command.category, description: command.description }) }
  public requestExplanation(questionId: string, attemptId?: string): Promise<QuestionExplanation> { return postData(path(questionId) + '/explanations', attemptId ? { attempt_id: attemptId } : {}) }
  public latestExplanation(questionId: string): Promise<QuestionExplanation> { return getData(path(questionId) + '/explanations/latest') }
}
