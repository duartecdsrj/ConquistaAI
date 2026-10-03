import type { BasicStatistics, PerformanceRepository, SubmittedAnswer, SyllabusDashboard, StudyMap, SaveStudyMapSchedule, StudyMapScheduleItem } from '../../Domain/Performance/PerformanceRepository'
import { getData, postData, putData } from '../Http/AxiosApiClient'
interface AttemptApi { readonly id: string }
interface AnswerApi { readonly id: string }
interface CompletedAttemptApi { readonly id: string; readonly completed_at: string }
export class AxiosPerformanceRepository implements PerformanceRepository {
  public getDashboard(syllabusId?: string, examId?: string): Promise<SyllabusDashboard> { return getData<SyllabusDashboard>('/dashboard/me', { params: { ...(syllabusId ? { syllabus_id: syllabusId } : {}), ...(examId ? { exam_id: examId } : {}) } }) }
  public getStudyMap(examId:string, from?:string, to?:string):Promise<StudyMap>{ return getData<StudyMap>("/performance/study-map",{params:{exam_id:examId,...(from?{from}:{}),...(to?{to}:{})}}) }
  public saveStudyMapSchedule(input:SaveStudyMapSchedule):Promise<StudyMapScheduleItem>{ return putData<StudyMapScheduleItem,SaveStudyMapSchedule>("/performance/study-map/schedule",input) }
  public getMine(): Promise<BasicStatistics> { return getData<BasicStatistics>('/statistics/me') }
  public async submitAnswer(notebookId: string, questionId: string, optionId: string, elapsedSeconds: number): Promise<SubmittedAnswer> {
    const attempt = await postData<AttemptApi, undefined>('/notebooks/' + encodeURIComponent(notebookId) + '/questions/' + encodeURIComponent(questionId) + '/attempts')
    const answer = await postData<AnswerApi, { option_id: string; elapsed_seconds: number }>('/attempts/' + encodeURIComponent(attempt.id) + '/answers', { option_id: optionId, elapsed_seconds: elapsedSeconds })
    const completed = await postData<CompletedAttemptApi, undefined>('/attempts/' + encodeURIComponent(attempt.id) + '/complete')
    return { attemptId: completed.id, answerId: answer.id, completedAt: completed.completed_at }
  }
}
