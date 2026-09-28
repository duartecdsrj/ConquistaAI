import type { AuthRepository, AuthSession, AuthenticatedUser, GoogleLoginCommand, GoogleLoginResult, LoginCredentials } from '../../Domain/Identity/AuthRepository'
import { getData, postData } from '../Http/AxiosApiClient'

interface LoginApiResponse { readonly access_token: string; readonly user: AuthenticatedUser }
interface PendingGoogleApiResponse { readonly status: 'PENDING_APPROVAL'; readonly message: string }
type GoogleLoginApiResponse = LoginApiResponse | PendingGoogleApiResponse

export class AxiosAuthRepository implements AuthRepository {
  public async login(credentials: LoginCredentials): Promise<AuthSession> {
    const response = await postData<LoginApiResponse, { email: string; password: string; device_name: string }>('/auth/login', { email: credentials.email, password: credentials.password, device_name: credentials.deviceName })
    return { accessToken: response.access_token, user: response.user }
  }

  public async googleLogin(command: GoogleLoginCommand): Promise<GoogleLoginResult> {
    const response = await postData<GoogleLoginApiResponse, { credential: string; device_name: string }>('/auth/google', { credential: command.credential, device_name: command.deviceName })
    if ('access_token' in response) return { kind: 'authenticated', session: { accessToken: response.access_token, user: response.user } }
    return { kind: 'pending_approval', approval: { status: response.status, message: response.message } }
  }

  public async refresh(): Promise<AuthSession> { const response = await postData<LoginApiResponse, { device_name: string }>('/auth/refresh', { device_name: 'web' }); return { accessToken: response.access_token, user: response.user } }
  public currentUser(): Promise<AuthenticatedUser> { return getData<AuthenticatedUser>('/auth/me') }
  public async logout(): Promise<void> { await postData<Record<string, never>, undefined>('/auth/logout') }
}
