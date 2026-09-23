import { LoginUseCase, LogoutUseCase, RestoreSessionUseCase } from '../Application/Identity/AuthUseCases'
import { GetMyStatisticsUseCase, SubmitNotebookAnswerUseCase } from '../Application/Performance/PerformanceUseCases'
import { ListPublishedQuestionsUseCase } from '../Application/QuestionBank/QuestionUseCases'
import { CreateNotebookUseCase, GetNotebookUseCase, ListNotebookQuestionsUseCase, ListNotebooksUseCase } from '../Application/Study/StudyUseCases'
import { configureAccessTokenProvider } from './Http/AxiosApiClient'
import { AxiosAuthRepository } from './Identity/AxiosAuthRepository'
import { CatalogUseCases } from '../Application/Catalog/CatalogUseCases'
import { AxiosCatalogRepository } from './Catalog/AxiosCatalogRepository'
import { BrowserSessionStore } from './Identity/BrowserSessionStore'
import { AxiosPerformanceRepository } from './Performance/AxiosPerformanceRepository'
import { AxiosQuestionRepository } from './QuestionBank/AxiosQuestionRepository'
import { AxiosStudyRepository } from './Study/AxiosStudyRepository'

const sessionStore = new BrowserSessionStore()
configureAccessTokenProvider(() => sessionStore.accessToken())

const studyRepository = new AxiosStudyRepository()
export const identityUseCases = {
  login: new LoginUseCase(new AxiosAuthRepository(), sessionStore),
  logout: new LogoutUseCase(new AxiosAuthRepository(), sessionStore),
  restoreSession: new RestoreSessionUseCase(new AxiosAuthRepository(), sessionStore),
}
export const studyUseCases = {
  list: new ListNotebooksUseCase(studyRepository),
  get: new GetNotebookUseCase(studyRepository),
  listQuestions: new ListNotebookQuestionsUseCase(studyRepository),
  create: new CreateNotebookUseCase(studyRepository),
}
export const questionUseCases = { listPublished: new ListPublishedQuestionsUseCase(new AxiosQuestionRepository()) }
const performanceRepository = new AxiosPerformanceRepository()
export const performanceUseCases = { getMine: new GetMyStatisticsUseCase(performanceRepository), submitAnswer: new SubmitNotebookAnswerUseCase(performanceRepository) }
export const catalogUseCases = new CatalogUseCases(new AxiosCatalogRepository())
