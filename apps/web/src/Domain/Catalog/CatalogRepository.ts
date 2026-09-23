export interface Exam { readonly id: string; readonly name: string; readonly organizer: string | null; readonly year: number | null }
export interface Position { readonly id: string; readonly examId: string; readonly name: string; readonly emphasis: string | null }
export interface Syllabus { readonly id: string; readonly positionId: string; readonly name: string; readonly publishedAt: string | null; readonly sourceUrl: string | null }
export interface Subject { readonly id: string; readonly syllabusId: string; readonly parentId: string | null; readonly name: string; readonly sortOrder: number }
export interface Tag { readonly id: string; readonly name: string }
export interface CatalogRepository {
  listExams(): Promise<readonly Exam[]>
  createExam(input: Omit<Exam, 'id'>): Promise<Exam>
  listPositions(examId: string): Promise<readonly Position[]>
  createPosition(examId: string, name: string, emphasis?: string): Promise<Position>
  listSyllabi(positionId: string): Promise<readonly Syllabus[]>
  createSyllabus(positionId: string, name: string, publishedAt?: string, sourceUrl?: string): Promise<Syllabus>
  listSubjects(syllabusId: string): Promise<readonly Subject[]>
  createSubject(syllabusId: string, name: string): Promise<Subject>
  listTags(): Promise<readonly Tag[]>
  createTag(name: string): Promise<Tag>
}
