import type { ImportReport, ImportRepository } from '../../Domain/Import/ImportRepository'
import { postData } from '../Http/AxiosApiClient'
export class AxiosImportRepository implements ImportRepository {
  public preview(format: 'JSON' | 'CSV', content: string): Promise<ImportReport> { return postData<ImportReport, { format: string; content: string }>('/question-imports', { format, content }) }
  public commit(importId: string, syllabusId: string): Promise<{ readonly importId: string; readonly createdQuestions: number }> { return postData('/question-imports/' + encodeURIComponent(importId) + '/commit', { syllabus_id: syllabusId }) }
}
