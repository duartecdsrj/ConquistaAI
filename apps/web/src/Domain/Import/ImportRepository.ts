export interface ImportRow { readonly rowNumber: number; readonly valid: boolean; readonly errors: readonly { readonly field: string; readonly code: string; readonly message: string }[] }
export interface ImportReport { readonly importId: string | null; readonly validRows: number; readonly invalidRows: number; readonly rows: readonly ImportRow[] }
export interface ImportRepository { preview(format: 'JSON' | 'CSV', content: string): Promise<ImportReport>; commit(importId: string, syllabusId: string): Promise<{ readonly importId: string; readonly createdQuestions: number }> }
