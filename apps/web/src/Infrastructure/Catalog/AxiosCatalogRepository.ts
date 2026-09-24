import type { CatalogRepository, Exam, Position, Subject, SubjectProvenance, Syllabus, SyllabusDocumentExtraction, SyllabusProcessingJob, Tag } from '../../Domain/Catalog/CatalogRepository'
import { getData, postData, postFormData, putData } from '../Http/AxiosApiClient'
export class AxiosCatalogRepository implements CatalogRepository {
  public listExams(): Promise<readonly Exam[]> { return getData<readonly Exam[]>('/exams') }
  public createExam(input: Omit<Exam, 'id'>): Promise<Exam> { return postData<Exam, { name: string; organizer: string | null; year: number | null }>('/exams', input) }
  public listPositions(examId: string): Promise<readonly Position[]> { return getData<readonly Position[]>('/exams/' + encodeURIComponent(examId) + '/positions') }
  public createPosition(examId: string, name: string, emphasis?: string): Promise<Position> { return postData<Position, { name: string; emphasis?: string }>('/exams/' + encodeURIComponent(examId) + '/positions', { name, ...(emphasis ? { emphasis } : {}) }) }
  public listSyllabi(positionId: string): Promise<readonly Syllabus[]> { return getData<readonly Syllabus[]>('/positions/' + encodeURIComponent(positionId) + '/syllabi') }
  public createSyllabus(positionId: string, name: string, publishedAt?: string, sourceUrl?: string): Promise<Syllabus> { return postData<Syllabus, Record<string, string>>('/positions/' + encodeURIComponent(positionId) + '/syllabi', { name, ...(publishedAt ? { published_at: publishedAt } : {}), ...(sourceUrl ? { source_url: sourceUrl } : {}) }) }
  public async uploadSyllabusDocument(syllabusId: string, document: File): Promise<void> { const form = new FormData(); form.append('document', document, document.name); await postFormData<{ id: string; document_sha256: string }>('/admin/syllabi/' + encodeURIComponent(syllabusId) + '/document', form) }
  public queueSyllabusProcessing(syllabusId: string, reprocess = false): Promise<SyllabusProcessingJob> { return postData<SyllabusProcessingJob, { reprocess: boolean }>('/admin/syllabi/' + encodeURIComponent(syllabusId) + '/processing-jobs', { reprocess }) }
  public getLatestSyllabusProcessing(syllabusId: string): Promise<SyllabusProcessingJob> { return getData<SyllabusProcessingJob>('/admin/syllabi/' + encodeURIComponent(syllabusId) + '/processing-jobs/latest') }
  public listSyllabusExtractions(syllabusId: string): Promise<readonly SyllabusDocumentExtraction[]> { return getData<readonly SyllabusDocumentExtraction[]>('/admin/syllabi/' + encodeURIComponent(syllabusId) + '/extractions') }
  public listSubjects(syllabusId: string): Promise<readonly Subject[]> { return getData<readonly Subject[]>('/syllabi/' + encodeURIComponent(syllabusId) + '/subjects') }
  public createSubject(syllabusId: string, name: string, provenance: SubjectProvenance = {}): Promise<Subject> { return postData<Subject, { syllabus_id: string; name: string; source_excerpt?: string; source_page?: number }>('/subjects', { syllabus_id: syllabusId, name, ...(provenance.sourceExcerpt ? { source_excerpt: provenance.sourceExcerpt } : {}), ...(provenance.sourcePage ? { source_page: provenance.sourcePage } : {}) }) }
  public async assignTaxonomySubjects(subjectId: string, taxonomySubjectIds: readonly string[]): Promise<void> { await putData<{ subject_id: string; taxonomy_subject_ids: readonly string[] }, { taxonomy_subject_ids: readonly string[] }>('/admin/subjects/' + encodeURIComponent(subjectId) + '/taxonomy-subjects', { taxonomy_subject_ids: taxonomySubjectIds }) }
  public listTags(): Promise<readonly Tag[]> { return getData<readonly Tag[]>('/tags') }
  public createTag(name: string): Promise<Tag> { return postData<Tag, { name: string }>('/tags', { name }) }
}
