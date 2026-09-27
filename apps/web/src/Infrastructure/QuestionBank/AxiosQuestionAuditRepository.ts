import type { QuestionAuditFinding, QuestionAuditFindingPage, QuestionAuditFindingQuery, QuestionAuditReport, QuestionAuditRepository } from '../../Domain/QuestionBank/QuestionAuditRepository'
import { getData, getPage } from '../Http/AxiosApiClient'
interface ApiReport { readonly id:string; readonly algorithmVersion:string; readonly scope:string; readonly status:string; readonly summary:Readonly<Record<string,number>>; readonly createdAt:string; readonly startedAt:string|null; readonly finishedAt:string|null; readonly errorMessage:string|null }
export class AxiosQuestionAuditRepository implements QuestionAuditRepository {
  public async latest(): Promise<QuestionAuditReport> {
    return getData<ApiReport>('/admin/question-audits/latest')
  }
  public async findings(query: QuestionAuditFindingQuery = {}): Promise<QuestionAuditFindingPage> {
    const page = await getPage<QuestionAuditFinding>('/admin/question-audits/latest/findings', { page: query.page, perPage: query.perPage })
    return { items: page.items, page: page.pagination.page, perPage: page.pagination.per_page, total: page.pagination.total, totalPages: page.pagination.total_pages }
  }
}
