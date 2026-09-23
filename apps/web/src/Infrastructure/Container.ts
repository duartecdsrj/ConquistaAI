import { LoginUseCase, LogoutUseCase, RestoreSessionUseCase } from '../Application/Identity/AuthUseCases'
import { configureAccessTokenProvider } from './Http/AxiosApiClient'
import { AxiosAuthRepository } from './Identity/AxiosAuthRepository'
import { BrowserSessionStore } from './Identity/BrowserSessionStore'

const sessionStore = new BrowserSessionStore()
const authRepository = new AxiosAuthRepository()

configureAccessTokenProvider(() => sessionStore.accessToken())

export const identityUseCases = {
  login: new LoginUseCase(authRepository, sessionStore),
  logout: new LogoutUseCase(authRepository, sessionStore),
  restoreSession: new RestoreSessionUseCase(authRepository, sessionStore),
}
