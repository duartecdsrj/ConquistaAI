import type { CreateManagedUserCommand, ManagedUser, ManagedUserRole, ManagedUserStatus, UserManagementPageQuery, UserManagementPageResult, UserManagementRepository } from '../../Domain/UserManagement/UserManagementRepository'
import { deleteData, getPage, patchData, postData, putData } from '../Http/AxiosApiClient'

type ApiUser = { readonly id: string; readonly name: string; readonly email: string; readonly googleEmail: string | null; readonly roles: readonly ManagedUserRole[]; readonly status: ManagedUserStatus }

export class AxiosUserManagementRepository implements UserManagementRepository {
  public async list(query: UserManagementPageQuery): Promise<UserManagementPageResult> {
    const page = await getPage<ApiUser>('/admin/users', { page: query.page, perPage: query.perPage }, { query: query.query, status: query.status })
    return { items: page.items.map((user) => this.user(user)), page: page.pagination.page, perPage: page.pagination.per_page, total: page.pagination.total, totalPages: page.pagination.total_pages }
  }
  public async create(command: CreateManagedUserCommand): Promise<ManagedUser> { return this.user(await postData<ApiUser, Record<string, unknown>>('/admin/users', { name: command.name, email: command.email, roles: command.roles, status: command.status, ...(command.password ? { password: command.password } : {}), ...(command.googleEmail ? { google_email: command.googleEmail } : {}) })) }
  public async setGoogleIdentity(userId: string, googleEmail: string): Promise<ManagedUser> { return this.user(await putData<ApiUser, { google_email: string }>(`/admin/users/${encodeURIComponent(userId)}/google-identity`, { google_email: googleEmail })) }
  public async removeGoogleIdentity(userId: string): Promise<ManagedUser> { return this.user(await deleteData<ApiUser>(`/admin/users/${encodeURIComponent(userId)}/google-identity`)) }
  public async updateRoles(userId: string, roles: readonly ManagedUserRole[]): Promise<ManagedUser> { return this.user(await patchData<ApiUser, { roles: readonly ManagedUserRole[] }>(`/admin/users/${encodeURIComponent(userId)}/roles`, { roles })) }
  public async updateStatus(userId: string, status: ManagedUserStatus): Promise<ManagedUser> { return this.user(await patchData<ApiUser, { status: ManagedUserStatus }>(`/admin/users/${encodeURIComponent(userId)}/status`, { status })) }
  private user(user: ApiUser): ManagedUser { return { ...user, roles: [...user.roles] } }
}
