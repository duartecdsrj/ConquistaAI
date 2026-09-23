import type { ImportReport, ImportRepository } from '../../Domain/Import/ImportRepository'
export class ImportUseCases {
  public constructor(private readonly repository: ImportRepository) {}
  public preview(format: 'JSON' | 'CSV', content: string): Promise<ImportReport> { if (!content.trim()) return Promise.reject(new Error('Informe o conteúdo para importar.')); return this.repository.preview(format, content) }
  public commit(importId: string, syllabusId: string): Promise<{ readonly importId: string; readonly createdQuestions: number }> { if (!syllabusId.trim()) return Promise.reject(new Error('Informe o identificador do edital.')); return this.repository.commit(importId, syllabusId) }
}
