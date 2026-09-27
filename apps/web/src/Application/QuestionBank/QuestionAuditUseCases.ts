import type { QuestionAuditFindingPage, QuestionAuditFindingQuery, QuestionAuditReport, QuestionAuditRepository } from '../../Domain/QuestionBank/QuestionAuditRepository'
export class GetLatestQuestionAuditReportUseCase {
  public constructor(private readonly repository: QuestionAuditRepository) {}
  public execute(): Promise<QuestionAuditReport> { return this.repository.latest() }
}
export class ListLatestQuestionAuditFindingsUseCase {
  public constructor(private readonly repository: QuestionAuditRepository) {}
  public execute(query?: QuestionAuditFindingQuery): Promise<QuestionAuditFindingPage> { return this.repository.findings(query) }
}
