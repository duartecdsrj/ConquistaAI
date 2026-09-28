import { computed, readonly, ref } from 'vue'
import type { AuthenticatedUser } from '../../../Domain/Identity/AuthRepository'
import { googleIdentityServices, identityUseCases } from '../../../Infrastructure/Container'

export function useAuth() {
  const user = ref<AuthenticatedUser | null>(null)
  const loading = ref(false)
  const submitting = ref(false)
  const error = ref('')
  const pendingApproval = ref('')
  const authenticated = computed(() => user.value !== null)
  const googleEnabled = computed(() => googleIdentityServices.isConfigured())

  async function restore(): Promise<void> {
    error.value = ''
    try { user.value = await identityUseCases.restoreSession.execute() } catch { user.value = null } finally { loading.value = false }
  }

  async function login(email: string, password: string): Promise<void> {
    error.value = ''; pendingApproval.value = ''; submitting.value = true
    try { user.value = (await identityUseCases.login.execute({ email, password, deviceName: 'web' })).user }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível entrar.' }
    finally { submitting.value = false }
  }

  async function loginGoogle(credential: string): Promise<void> {
    error.value = ''; pendingApproval.value = ''; submitting.value = true
    try {
      const result = await identityUseCases.googleLogin.execute({ credential, deviceName: 'web' })
      if (result.kind === 'authenticated') user.value = result.session.user
      else pendingApproval.value = result.approval.message
    } catch (reason) {
      error.value = reason instanceof Error ? reason.message : 'Não foi possível entrar com Google.'
    } finally { submitting.value = false }
  }

  async function initializeGoogleButton(element: HTMLElement): Promise<void> {
    try { await googleIdentityServices.renderButton(element, (credential) => { void loginGoogle(credential) }) }
    catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar o login Google.' }
  }

  async function logout(): Promise<void> { await identityUseCases.logout.execute(); user.value = null }

  return { user: readonly(user), loading: readonly(loading), submitting: readonly(submitting), error: readonly(error), pendingApproval: readonly(pendingApproval), authenticated, googleEnabled, restore, login, initializeGoogleButton, logout }
}
