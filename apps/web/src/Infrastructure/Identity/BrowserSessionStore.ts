import type { AuthSession, SessionStore } from '../../Domain/Identity/AuthRepository'

const ACCESS_TOKEN_KEY = 'concursos_access_token'

export class BrowserSessionStore implements SessionStore {
  public accessToken(): string | null {
    return localStorage.getItem(ACCESS_TOKEN_KEY)
  }

  public save(session: AuthSession): void {
    localStorage.setItem(ACCESS_TOKEN_KEY, session.accessToken)
  }

  public clear(): void {
    localStorage.removeItem(ACCESS_TOKEN_KEY)
  }
}
