export interface QuestionAuditReport {
  readonly id: string
  readonly algorithmVersion: string
  readonly scope: string
  readonly status: string
  readonly summary: Readonly<Record<string, number>>
  readonly createdAt: string
  readonly startedAt: string | null
  readonly finishedAt: string | null
  readonly errorMessage: string | null
}
export interface QuestionAuditFinding { readonly id:string; readonly questionId:string; readonly sourcePdfJobId:string|null; readonly sourcePage:number|null; readonly code:string; readonly confidence:string; readonly status:string; readonly message:string; readonly createdAt:string; readonly structureAfter:Readonly<Record<string,string>>|null }
export interface QuestionAuditFindingPage { readonly items:readonly QuestionAuditFinding[]; readonly page:number; readonly perPage:number; readonly total:number; readonly totalPages:number }
export interface QuestionAuditFindingQuery { readonly page?:number; readonly perPage?:number }
export interface QuestionAuditRepository {
  latest(): Promise<QuestionAuditReport>
  findings(query?: QuestionAuditFindingQuery): Promise<QuestionAuditFindingPage>
}
