export interface AuthenticatedUser {
  readonly id: string
  readonly email: string
  readonly name: string
  readonly roles: readonly string[]
}

export interface LoginCredentials {
  readonly email: string
  readonly password: string
  readonly deviceName: string
}

export interface AuthSession {
  readonly accessToken: string
  readonly user: AuthenticatedUser
}

export interface AuthRepository {
  login(credentials: LoginCredentials): Promise<AuthSession>
  refresh(): Promise<AuthSession>
  currentUser(): Promise<AuthenticatedUser>
  logout(): Promise<void>
}

export interface SessionStore {
  accessToken(): string | null
  save(session: AuthSession): void
  clear(): void
}
