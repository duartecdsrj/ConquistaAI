export interface SubjectPerformance { readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number }
export interface BasicStatistics { readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number; readonly averageElapsedSeconds: number; readonly subjects: Readonly<Record<string, SubjectPerformance>> }
export interface SubmittedAnswer { readonly attemptId: string; readonly answerId: string; readonly completedAt: string }
export interface SyllabusOption { readonly id: string; readonly name: string; readonly positionName: string; readonly examName: string }
export interface HierarchicalSubjectPerformance extends SubjectPerformance { readonly id: string; readonly parentId: string | null; readonly name: string; readonly distinctDays: number; readonly sufficientData: boolean }
export interface SyllabusDashboard { readonly syllabi: readonly SyllabusOption[]; readonly selectedSyllabusId: string | null; readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number; readonly averageElapsedSeconds: number; readonly sufficientData: boolean; readonly distinctDays: number; readonly subjects: readonly HierarchicalSubjectPerformance[] }
export interface PerformanceRepository {
  getDashboard(syllabusId?: string, examId?: string): Promise<SyllabusDashboard>
  getMine(): Promise<BasicStatistics>
  submitAnswer(notebookId: string, questionId: string, optionId: string, elapsedSeconds: number): Promise<SubmittedAnswer>
}
