import { computed, readonly, ref } from 'vue'
import type { AuthenticatedUser } from '../../../Domain/Identity/AuthRepository'
import { identityUseCases } from '../../../Infrastructure/Container'

export function useAuth() {
  const user = ref<AuthenticatedUser | null>(null)
  const loading = ref(true)
  const submitting = ref(false)
  const error = ref('')

  const authenticated = computed(() => user.value !== null)

  async function restore(): Promise<void> {
    loading.value = true
    user.value = await identityUseCases.restoreSession.execute()
    loading.value = false
  }

  async function login(email: string, password: string): Promise<void> {
    error.value = ''
    submitting.value = true
    try {
      user.value = (await identityUseCases.login.execute({ email, password, deviceName: 'web' })).user
    } catch (reason) {
      error.value = reason instanceof Error ? reason.message : 'Nao foi possivel entrar.'
    } finally {
      submitting.value = false
    }
  }

  async function logout(): Promise<void> {
    await identityUseCases.logout.execute()
    user.value = null
  }

  return {
    user: readonly(user),
    loading: readonly(loading),
    submitting: readonly(submitting),
    error: readonly(error),
    authenticated,
    restore,
    login,
    logout,
  }
}
