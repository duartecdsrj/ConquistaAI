import type { CatalogRepository, Exam, Position, Subject, SubjectProvenance, Syllabus, Tag } from '../../Domain/Catalog/CatalogRepository'

export class CatalogUseCases {
  public constructor(private readonly repository: CatalogRepository) {}
  public listExams(): Promise<readonly Exam[]> { return this.repository.listExams() }
  public createExam(name: string, organizer: string, year: number | null): Promise<Exam> { return this.repository.createExam({ name: required(name, 'Informe o nome do concurso.'), organizer: optional(organizer), year }) }
  public listPositions(examId: string): Promise<readonly Position[]> { return this.repository.listPositions(examId) }
  public createPosition(examId: string, name: string, emphasis: string): Promise<Position> { return this.repository.createPosition(examId, required(name, 'Informe o nome do cargo.'), optional(emphasis) ?? undefined) }
  public listSyllabi(positionId: string): Promise<readonly Syllabus[]> { return this.repository.listSyllabi(positionId) }
  public createSyllabus(positionId: string, name: string): Promise<Syllabus> { return this.repository.createSyllabus(positionId, required(name, 'Informe o nome do edital.')) }
  public uploadSyllabusDocument(syllabusId: string, document: File): Promise<void> {
    if (document.type !== 'application/pdf') throw new Error('Selecione um arquivo PDF.')
    return this.repository.uploadSyllabusDocument(syllabusId, document)
  }
  public listSubjects(syllabusId: string): Promise<readonly Subject[]> { return this.repository.listSubjects(syllabusId) }
  public createSubject(syllabusId: string, name: string, provenance: SubjectProvenance = {}): Promise<Subject> { return this.repository.createSubject(syllabusId, required(name, 'Informe o nome do assunto.'), provenance) }
  public listTags(): Promise<readonly Tag[]> { return this.repository.listTags() }
  public createTag(name: string): Promise<Tag> { return this.repository.createTag(required(name, 'Informe o nome da tag.')) }
}
function required(value: string, message: string): string { const normalized = value.trim(); if (!normalized) throw new Error(message); return normalized }
function optional(value: string): string | null { const normalized = value.trim(); return normalized || null }
