import type { BasicStatistics, PerformanceRepository, SubmittedAnswer, SyllabusDashboard } from '../../Domain/Performance/PerformanceRepository'
import { getData, postData } from '../Http/AxiosApiClient'
interface AttemptApi { readonly id: string }
interface AnswerApi { readonly id: string }
interface CompletedAttemptApi { readonly id: string; readonly completed_at: string }
export class AxiosPerformanceRepository implements PerformanceRepository {
  public getDashboard(syllabusId?: string): Promise<SyllabusDashboard> { return getData<SyllabusDashboard>('/dashboard/me', { params: syllabusId ? { syllabus_id: syllabusId } : {} }) }
  public getMine(): Promise<BasicStatistics> { return getData<BasicStatistics>('/statistics/me') }
  public async submitAnswer(notebookId: string, questionId: string, optionId: string, elapsedSeconds: number): Promise<SubmittedAnswer> {
    const attempt = await postData<AttemptApi, undefined>('/notebooks/' + encodeURIComponent(notebookId) + '/questions/' + encodeURIComponent(questionId) + '/attempts')
    const answer = await postData<AnswerApi, { option_id: string; elapsed_seconds: number }>('/attempts/' + encodeURIComponent(attempt.id) + '/answers', { option_id: optionId, elapsed_seconds: elapsedSeconds })
    const completed = await postData<CompletedAttemptApi, undefined>('/attempts/' + encodeURIComponent(attempt.id) + '/complete')
    return { attemptId: completed.id, answerId: answer.id, completedAt: completed.completed_at }
  }
}
