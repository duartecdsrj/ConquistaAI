import type { CreateManagedUserCommand, ManagedUser, ManagedUserRole, ManagedUserStatus, UserManagementPageQuery, UserManagementPageResult, UserManagementRepository } from '../../Domain/UserManagement/UserManagementRepository'

export class UserManagementUseCases {
  public constructor(private readonly repository: UserManagementRepository) {}
  public list(query: UserManagementPageQuery): Promise<UserManagementPageResult> { return this.repository.list({ ...query, page: Math.max(1, query.page), perPage: Math.min(100, Math.max(1, query.perPage)), query: query.query?.trim() || undefined }) }
  public create(command: CreateManagedUserCommand): Promise<ManagedUser> { return this.repository.create({ ...command, name: command.name.trim(), email: command.email.trim().toLowerCase(), googleEmail: command.googleEmail?.trim().toLowerCase() || undefined }) }
  public setGoogleIdentity(userId: string, googleEmail: string): Promise<ManagedUser> { return this.repository.setGoogleIdentity(userId, googleEmail.trim().toLowerCase()) }
  public removeGoogleIdentity(userId: string): Promise<ManagedUser> { return this.repository.removeGoogleIdentity(userId) }
  public updateRoles(userId: string, roles: readonly ManagedUserRole[]): Promise<ManagedUser> { return this.repository.updateRoles(userId, roles) }
  public updateStatus(userId: string, status: ManagedUserStatus): Promise<ManagedUser> { return this.repository.updateStatus(userId, status) }
}
