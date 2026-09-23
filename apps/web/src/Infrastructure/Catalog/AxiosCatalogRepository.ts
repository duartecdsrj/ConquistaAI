import type { CatalogRepository, Exam, Position, Subject, Syllabus, Tag } from '../../Domain/Catalog/CatalogRepository'
import { getData, postData } from '../Http/AxiosApiClient'
export class AxiosCatalogRepository implements CatalogRepository {
  public listExams(): Promise<readonly Exam[]> { return getData<readonly Exam[]>('/exams') }
  public createExam(input: Omit<Exam, 'id'>): Promise<Exam> { return postData<Exam, { name: string; organizer: string | null; year: number | null }>('/exams', input) }
  public listPositions(examId: string): Promise<readonly Position[]> { return getData<readonly Position[]>('/exams/' + encodeURIComponent(examId) + '/positions') }
  public createPosition(examId: string, name: string, emphasis?: string): Promise<Position> { return postData<Position, { name: string; emphasis?: string }>('/exams/' + encodeURIComponent(examId) + '/positions', { name, ...(emphasis ? { emphasis } : {}) }) }
  public listSyllabi(positionId: string): Promise<readonly Syllabus[]> { return getData<readonly Syllabus[]>('/positions/' + encodeURIComponent(positionId) + '/syllabi') }
  public createSyllabus(positionId: string, name: string, publishedAt?: string, sourceUrl?: string): Promise<Syllabus> { return postData<Syllabus, Record<string, string>>('/positions/' + encodeURIComponent(positionId) + '/syllabi', { name, ...(publishedAt ? { published_at: publishedAt } : {}), ...(sourceUrl ? { source_url: sourceUrl } : {}) }) }
  public listSubjects(syllabusId: string): Promise<readonly Subject[]> { return getData<readonly Subject[]>('/syllabi/' + encodeURIComponent(syllabusId) + '/subjects') }
  public createSubject(syllabusId: string, name: string): Promise<Subject> { return postData<Subject, { syllabus_id: string; name: string }>('/subjects', { syllabus_id: syllabusId, name }) }
  public listTags(): Promise<readonly Tag[]> { return getData<readonly Tag[]>('/tags') }
  public createTag(name: string): Promise<Tag> { return postData<Tag, { name: string }>('/tags', { name }) }
}
