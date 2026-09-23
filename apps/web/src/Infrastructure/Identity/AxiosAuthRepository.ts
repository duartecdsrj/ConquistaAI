import type { AuthRepository, AuthSession, AuthenticatedUser, LoginCredentials } from '../../Domain/Identity/AuthRepository'
import { getData, postData } from '../Http/AxiosApiClient'

interface LoginApiResponse {
  readonly access_token: string
  readonly user: AuthenticatedUser
}

export class AxiosAuthRepository implements AuthRepository {
  public async login(credentials: LoginCredentials): Promise<AuthSession> {
    const response = await postData<LoginApiResponse, { email: string; password: string; device_name: string }>(
      '/auth/login',
      { email: credentials.email, password: credentials.password, device_name: credentials.deviceName },
    )
    return { accessToken: response.access_token, user: response.user }
  }

  public currentUser(): Promise<AuthenticatedUser> {
    return getData<AuthenticatedUser>('/auth/me')
  }

  public async logout(): Promise<void> {
    await postData<Record<string, never>, undefined>('/auth/logout')
  }
}
