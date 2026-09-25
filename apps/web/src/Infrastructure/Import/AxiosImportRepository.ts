import type { ImportReport, ImportRepository, QuestionPdfImportJob, QuestionPdfImportJobPage } from '../../Domain/Import/ImportRepository'
import { getData, getPage, postData, postFormData } from '../Http/AxiosApiClient'
export class AxiosImportRepository implements ImportRepository {
  public preview(format: 'JSON' | 'CSV', content: string): Promise<ImportReport> { return postData<ImportReport, { format: string; content: string }>('/question-imports', { format, content }) }
  public commit(importId: string, syllabusId: string): Promise<{ readonly importId: string; readonly createdQuestions: number }> { return postData('/question-imports/' + encodeURIComponent(importId) + '/commit', { syllabus_id: syllabusId }) }
  public queuePdfImports(documents: readonly File[]): Promise<readonly QuestionPdfImportJob[]> { const form = new FormData(); documents.forEach((document) => form.append('documents[]', document)); return postFormData('/admin/question-pdf-imports', form) }
  public getPdfImportJob(id: string): Promise<QuestionPdfImportJob> { return getData('/admin/question-pdf-imports/' + encodeURIComponent(id)) }
  public async cancelPdfImportJob(id: string): Promise<void> { await postData('/admin/question-pdf-imports/' + encodeURIComponent(id) + '/cancel') }
  public async listPdfImportJobs(page = 1, perPage = 25): Promise<QuestionPdfImportJobPage> { const result = await getPage<QuestionPdfImportJob>('/admin/question-pdf-imports', { page, perPage }); return { items: result.items, page: result.pagination.page, perPage: result.pagination.per_page, total: result.pagination.total } }
}
