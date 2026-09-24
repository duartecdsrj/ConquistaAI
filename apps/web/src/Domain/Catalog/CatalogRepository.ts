export interface Exam { readonly id: string; readonly name: string; readonly organizer: string | null; readonly year: number | null }
export interface Position { readonly id: string; readonly examId: string; readonly name: string; readonly emphasis: string | null }
export interface Syllabus { readonly id: string; readonly positionId: string; readonly name: string; readonly publishedAt: string | null; readonly sourceUrl: string | null; readonly documentSha256: string | null; readonly documentOriginalName: string | null; readonly documentMimeType: string | null; readonly documentSize: number | null }
export interface Subject { readonly id: string; readonly syllabusId: string; readonly parentId: string | null; readonly name: string; readonly sortOrder: number; readonly sourceExcerpt: string | null; readonly sourcePage: number | null; readonly sourceStartOffset: number | null; readonly sourceEndOffset: number | null }
export interface SubjectProvenance { readonly sourceExcerpt?: string; readonly sourcePage?: number }
export interface SyllabusProcessingJob { readonly id: string; readonly syllabusId: string; readonly documentSha256: string; readonly status: 'PENDING' | 'PROCESSING' | 'COMPLETED' | 'FAILED'; readonly progress: number; readonly errorMessage: string | null; readonly createdAt: string; readonly startedAt: string | null; readonly finishedAt: string | null }
export interface SyllabusDocumentExtraction { readonly documentSha256: string; readonly pageNumber: number; readonly textContent: string; readonly startOffset: number; readonly endOffset: number }
export interface Tag { readonly id: string; readonly name: string }
export interface CatalogRepository {
  listExams(): Promise<readonly Exam[]>
  createExam(input: Omit<Exam, 'id'>): Promise<Exam>
  listPositions(examId: string): Promise<readonly Position[]>
  createPosition(examId: string, name: string, emphasis?: string): Promise<Position>
  listSyllabi(positionId: string): Promise<readonly Syllabus[]>
  createSyllabus(positionId: string, name: string, publishedAt?: string, sourceUrl?: string): Promise<Syllabus>
  uploadSyllabusDocument(syllabusId: string, document: File): Promise<void>
  queueSyllabusProcessing(syllabusId: string, reprocess?: boolean): Promise<SyllabusProcessingJob>
  getLatestSyllabusProcessing(syllabusId: string): Promise<SyllabusProcessingJob>
  listSyllabusExtractions(syllabusId: string): Promise<readonly SyllabusDocumentExtraction[]>
  listSubjects(syllabusId: string): Promise<readonly Subject[]>
  createSubject(syllabusId: string, name: string, provenance?: SubjectProvenance): Promise<Subject>
  assignTaxonomySubjects(subjectId: string, taxonomySubjectIds: readonly string[]): Promise<void>
  listTags(): Promise<readonly Tag[]>
  createTag(name: string): Promise<Tag>
}
