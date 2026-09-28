export type ManagedUserStatus = 'ACTIVE' | 'PENDING_APPROVAL' | 'BLOCKED'
export type ManagedUserRole = 'ADMIN' | 'USER'

export interface ManagedUser {
  readonly id: string
  readonly name: string
  readonly email: string
  readonly googleEmail: string | null
  readonly roles: readonly ManagedUserRole[]
  readonly status: ManagedUserStatus
}

export interface UserManagementPageQuery {
  readonly page: number
  readonly perPage: number
  readonly query?: string
  readonly status?: ManagedUserStatus
}

export interface UserManagementPageResult {
  readonly items: readonly ManagedUser[]
  readonly page: number
  readonly perPage: number
  readonly total: number
  readonly totalPages: number
}

export interface CreateManagedUserCommand {
  readonly name: string
  readonly email: string
  readonly roles: readonly ManagedUserRole[]
  readonly status: ManagedUserStatus
  readonly password?: string
  readonly googleEmail?: string
}

export interface UserManagementRepository {
  list(query: UserManagementPageQuery): Promise<UserManagementPageResult>
  create(command: CreateManagedUserCommand): Promise<ManagedUser>
  setGoogleIdentity(userId: string, googleEmail: string): Promise<ManagedUser>
  removeGoogleIdentity(userId: string): Promise<ManagedUser>
  updateRoles(userId: string, roles: readonly ManagedUserRole[]): Promise<ManagedUser>
  updateStatus(userId: string, status: ManagedUserStatus): Promise<ManagedUser>
}
