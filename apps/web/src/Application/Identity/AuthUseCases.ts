import type { AuthRepository, AuthSession, AuthenticatedUser, LoginCredentials, SessionStore } from '../../Domain/Identity/AuthRepository'

export class LoginUseCase {
  public constructor(private readonly repository: AuthRepository, private readonly sessionStore: SessionStore) {}

  public async execute(credentials: LoginCredentials): Promise<AuthSession> {
    if (credentials.email.trim() === '' || credentials.password === '') {
      throw new Error('Informe e-mail e senha.')
    }

    const session = await this.repository.login({
      ...credentials,
      email: credentials.email.trim().toLowerCase(),
    })
    this.sessionStore.save(session)
    return session
  }
}

export class RestoreSessionUseCase {
  public constructor(private readonly repository: AuthRepository, private readonly sessionStore: SessionStore) {}

  public async execute(): Promise<AuthenticatedUser | null> {
    if (!this.sessionStore.accessToken()) return null

    try {
      return await this.repository.currentUser()
    } catch {
      this.sessionStore.clear()
      return null
    }
  }
}

export class LogoutUseCase {
  public constructor(private readonly repository: AuthRepository, private readonly sessionStore: SessionStore) {}

  public async execute(): Promise<void> {
    try {
      await this.repository.logout()
    } finally {
      this.sessionStore.clear()
    }
  }
}
