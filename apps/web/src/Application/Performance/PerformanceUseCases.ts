import type { BasicStatistics, PerformanceRepository, SubmittedAnswer } from '../../Domain/Performance/PerformanceRepository'
export class GetMyStatisticsUseCase { public constructor(private readonly repository: PerformanceRepository) {} public execute(): Promise<BasicStatistics> { return this.repository.getMine() } }
export class GetSyllabusDashboardUseCase { public constructor(private readonly repository: PerformanceRepository) {} public execute(syllabusId?: string): Promise<import('../../Domain/Performance/PerformanceRepository').SyllabusDashboard> { return this.repository.getDashboard(syllabusId) } }
export class SubmitNotebookAnswerUseCase {
  public constructor(private readonly repository: PerformanceRepository) {}
  public execute(notebookId: string, questionId: string, optionId: string, elapsedSeconds: number): Promise<SubmittedAnswer> {
    if (!notebookId || !questionId || !optionId) return Promise.reject(new Error('Escolha uma alternativa antes de concluir.'))
    return this.repository.submitAnswer(notebookId, questionId, optionId, Math.max(0, Math.trunc(elapsedSeconds)))
  }
}
