export interface Exam { readonly id: string; readonly name: string; readonly organizer: string | null; readonly year: number | null; readonly institutionLogoUrl: string | null; readonly organizerLogoUrl: string | null }
export type ExamInput = Omit<Exam, 'id' | 'institutionLogoUrl' | 'organizerLogoUrl'>
export interface Position { readonly id: string; readonly examId: string; readonly name: string; readonly emphasis: string | null }
export interface Syllabus { readonly id: string; readonly examId: string; readonly name: string; readonly publishedAt: string | null; readonly sourceUrl: string | null; readonly documentSha256: string | null; readonly documentOriginalName: string | null; readonly documentMimeType: string | null; readonly documentSize: number | null }
export interface Subject { readonly id: string; readonly syllabusId: string; readonly parentId: string | null; readonly name: string; readonly sortOrder: number; readonly sourceExcerpt: string | null; readonly sourcePage: number | null; readonly sourceStartOffset: number | null; readonly sourceEndOffset: number | null; readonly selectionWeight: number; readonly weightSource: 'DEFAULT' | 'EDITAL' | 'HISTORICAL' | 'AI_ESTIMATED'; readonly weightConfidence: number; readonly weightCalculatedAt: string | null }
export interface SubjectProvenance { readonly sourceExcerpt?: string; readonly sourcePage?: number }
export interface SyllabusProcessingJob { readonly id: string; readonly syllabusId: string; readonly documentSha256: string; readonly status: 'PENDING' | 'PROCESSING' | 'COMPLETED' | 'FAILED'; readonly progress: number; readonly errorMessage: string | null; readonly createdAt: string; readonly startedAt: string | null; readonly finishedAt: string | null }
export interface SyllabusDocumentExtraction { readonly documentSha256: string; readonly pageNumber: number; readonly textContent: string; readonly startOffset: number; readonly endOffset: number }
export interface Tag { readonly id: string; readonly name: string }
export interface ExamWithNotice { readonly exam: Exam; readonly syllabus: Syllabus | null; readonly processingJob: SyllabusProcessingJob | null }
export interface CatalogRepository {
  listExams(): Promise<readonly Exam[]>
  createExam(input: ExamInput): Promise<Exam>
  updateExam(id: string, input: ExamInput): Promise<Exam>
  createExamWithNotice(input: ExamInput, document: File | null): Promise<ExamWithNotice>
  listPositions(examId: string): Promise<readonly Position[]>
  createPosition(examId: string, name: string, emphasis?: string): Promise<Position>
  listPositionTaxonomySubjects(positionId: string): Promise<readonly string[]>
  assignPositionTaxonomySubjects(positionId: string, taxonomySubjectIds: readonly string[]): Promise<void>
  listSyllabi(examId: string): Promise<readonly Syllabus[]>
  createSyllabus(examId: string, name: string, publishedAt?: string, sourceUrl?: string): Promise<Syllabus>
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
