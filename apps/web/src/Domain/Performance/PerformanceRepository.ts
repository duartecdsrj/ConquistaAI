export interface SubjectPerformance { readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number }
export interface BasicStatistics { readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number; readonly averageElapsedSeconds: number; readonly subjects: Readonly<Record<string, SubjectPerformance>> }
export interface SubmittedAnswer { readonly attemptId: string; readonly answerId: string; readonly completedAt: string }
export interface SyllabusOption { readonly id: string; readonly name: string; readonly positionName: string; readonly examName: string }
export interface HierarchicalSubjectPerformance extends SubjectPerformance { readonly id: string; readonly parentId: string | null; readonly name: string; readonly distinctDays: number; readonly sufficientData: boolean }
export interface SyllabusDashboard { readonly syllabi: readonly SyllabusOption[]; readonly selectedSyllabusId: string | null; readonly total: number; readonly correct: number; readonly incorrect: number; readonly percentage: number; readonly averageElapsedSeconds: number; readonly sufficientData: boolean; readonly distinctDays: number; readonly subjects: readonly HierarchicalSubjectPerformance[] }
export type StudyScheduleStatus = "PLANNED" | "STUDIED" | "COMPLETED"
export interface StudyMapScheduleItem { readonly subjectId:string; readonly startDate:string; readonly endDate:string; readonly status:StudyScheduleStatus; readonly completedAt:string|null; readonly predecessorSubjectIds:readonly string[] }
export interface StudyMapSubject { readonly id:string; readonly parentId:string|null; readonly name:string; readonly depth:number; readonly answered:number; readonly correct:number; readonly incorrect:number; readonly accuracy:number|null; readonly distinctDays:number; readonly evidenceStatus:"NO_DATA"|"INSUFFICIENT"|"SUFFICIENT"; readonly children:readonly StudyMapSubject[] }
export interface StudyMap { readonly examId:string; readonly from:string|null; readonly to:string|null; readonly answered:number; readonly correct:number; readonly incorrect:number; readonly accuracy:number|null; readonly unclassifiedAnswers:number; readonly subjects:readonly StudyMapSubject[]; readonly schedule:readonly StudyMapScheduleItem[] }
export interface SaveStudyMapSchedule { readonly examId:string; readonly subjectId:string; readonly startDate:string; readonly endDate:string; readonly status:StudyScheduleStatus; readonly predecessorSubjectIds:readonly string[] }
export interface PerformanceRepository {
  getDashboard(syllabusId?: string, examId?: string): Promise<SyllabusDashboard>
  getStudyMap(examId:string, from?:string, to?:string): Promise<StudyMap>
  saveStudyMapSchedule(input:SaveStudyMapSchedule): Promise<StudyMapScheduleItem>
  getMine(): Promise<BasicStatistics>
  submitAnswer(notebookId: string, questionId: string, optionId: string, elapsedSeconds: number): Promise<SubmittedAnswer>
}
