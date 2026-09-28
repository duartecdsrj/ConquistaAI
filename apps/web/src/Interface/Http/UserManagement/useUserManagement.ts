import { readonly, ref } from 'vue'
import type { CreateManagedUserCommand, ManagedUser, ManagedUserRole, ManagedUserStatus } from '../../../Domain/UserManagement/UserManagementRepository'
import { userManagementUseCases } from '../../../Infrastructure/Container'

export function useUserManagement() {
  const users = ref<readonly ManagedUser[]>([])
  const total = ref(0); const page = ref(1); const perPage = ref(25)
  const query = ref(''); const status = ref<ManagedUserStatus | undefined>()
  const loading = ref(false); const saving = ref(false); const error = ref('')

  async function load(nextPage = page.value): Promise<void> {
    loading.value = true; error.value = ''
    try { const result = await userManagementUseCases.list({ page: nextPage, perPage: perPage.value, query: query.value, status: status.value }); users.value = result.items; page.value = result.page; total.value = result.total }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar usuários.' }
    finally { loading.value = false }
  }

  async function save(operation: () => Promise<ManagedUser>): Promise<ManagedUser | null> {
    saving.value = true; error.value = ''
    try { const user = await operation(); await load(page.value); return user }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível salvar a alteração.'; return null }
    finally { saving.value = false }
  }
  function create(command: CreateManagedUserCommand): Promise<ManagedUser | null> { return save(() => userManagementUseCases.create(command)) }
  function setGoogleIdentity(userId: string, googleEmail: string): Promise<ManagedUser | null> { return save(() => userManagementUseCases.setGoogleIdentity(userId, googleEmail)) }
  function removeGoogleIdentity(userId: string): Promise<ManagedUser | null> { return save(() => userManagementUseCases.removeGoogleIdentity(userId)) }
  function updateRoles(userId: string, roles: readonly ManagedUserRole[]): Promise<ManagedUser | null> { return save(() => userManagementUseCases.updateRoles(userId, roles)) }
  function updateStatus(userId: string, nextStatus: ManagedUserStatus): Promise<ManagedUser | null> { return save(() => userManagementUseCases.updateStatus(userId, nextStatus)) }

  return { users: readonly(users), total: readonly(total), page: readonly(page), perPage: readonly(perPage), query, status, loading: readonly(loading), saving: readonly(saving), error: readonly(error), load, create, setGoogleIdentity, removeGoogleIdentity, updateRoles, updateStatus }
}
