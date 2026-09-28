const STORAGE_KEY = 'concursos_ignored_question_correction_results'
const MAX_IGNORED_RESULTS = 100

export class BrowserIgnoredQuestionCorrectionResultStore {
  public has(requestId: string): boolean {
    return this.read().includes(requestId)
  }

  public ignore(requestId: string): void {
    if (!requestId.trim()) return
    const ids = this.read().filter((id) => id !== requestId)
    ids.unshift(requestId)
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(ids.slice(0, MAX_IGNORED_RESULTS))) } catch { }
  }

  private read(): readonly string[] {
    try {
      const parsed: unknown = JSON.parse(localStorage.getItem(STORAGE_KEY) ?? '[]')
      return Array.isArray(parsed) ? parsed.filter((value): value is string => typeof value === 'string' && value.length > 0).slice(0, MAX_IGNORED_RESULTS) : []
    } catch {
      return []
    }
  }
}
