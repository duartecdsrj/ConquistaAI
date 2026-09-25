import type { ImportReport, ImportRepository, QuestionPdfImportJob, QuestionPdfImportJobPage } from '../../Domain/Import/ImportRepository'
export class ImportUseCases {
  public constructor(private readonly repository: ImportRepository) {}
  public preview(format: 'JSON' | 'CSV', content: string): Promise<ImportReport> { if (!content.trim()) return Promise.reject(new Error('Informe o conteúdo para importar.')); return this.repository.preview(format, content) }
  public commit(importId: string, syllabusId: string): Promise<{ readonly importId: string; readonly createdQuestions: number }> { if (!syllabusId.trim()) return Promise.reject(new Error('Informe o edital.')); return this.repository.commit(importId, syllabusId) }
  public queuePdfImports(documents: readonly File[]): Promise<readonly QuestionPdfImportJob[]> { if (!documents.length) return Promise.reject(new Error('Selecione ao menos um PDF.')); return this.repository.queuePdfImports(documents) }
  public getPdfImportJob(id: string): Promise<QuestionPdfImportJob> { return this.repository.getPdfImportJob(id) }
  public cancelPdfImportJob(id: string): Promise<void> { return this.repository.cancelPdfImportJob(id) }
  public listPdfImportJobs(page = 1, perPage = 25): Promise<QuestionPdfImportJobPage> { return this.repository.listPdfImportJobs(page, perPage) }
}
