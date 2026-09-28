export interface AuthenticatedUser {
  readonly id: string
  readonly email: string
  readonly name: string
  readonly roles: readonly string[]
  readonly status: 'ACTIVE' | 'PENDING_APPROVAL' | 'BLOCKED'
}

export interface LoginCredentials {
  readonly email: string
  readonly password: string
  readonly deviceName: string
}

export interface GoogleLoginCommand {
  readonly credential: string
  readonly deviceName: string
}

export interface AuthSession {
  readonly accessToken: string
  readonly user: AuthenticatedUser
}

export interface PendingApproval {
  readonly status: 'PENDING_APPROVAL'
  readonly message: string
}

export type GoogleLoginResult =
  | { readonly kind: 'authenticated'; readonly session: AuthSession }
  | { readonly kind: 'pending_approval'; readonly approval: PendingApproval }

export interface AuthRepository {
  login(credentials: LoginCredentials): Promise<AuthSession>
  googleLogin(command: GoogleLoginCommand): Promise<GoogleLoginResult>
  refresh(): Promise<AuthSession>
  currentUser(): Promise<AuthenticatedUser>
  logout(): Promise<void>
}

export interface SessionStore {
  accessToken(): string | null
  save(session: AuthSession): void
  clear(): void
}
