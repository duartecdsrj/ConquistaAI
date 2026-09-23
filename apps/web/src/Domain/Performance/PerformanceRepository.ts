export interface SubjectPerformance { readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number }
export interface BasicStatistics { readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number; readonly averageElapsedSeconds: number; readonly subjects: Readonly<Record<string, SubjectPerformance>> }
export interface SubmittedAnswer { readonly attemptId: string; readonly answerId: string; readonly completedAt: string }
export interface PerformanceRepository {
  getMine(): Promise<BasicStatistics>
  submitAnswer(notebookId: string, questionId: string, optionId: string, elapsedSeconds: number): Promise<SubmittedAnswer>
}
