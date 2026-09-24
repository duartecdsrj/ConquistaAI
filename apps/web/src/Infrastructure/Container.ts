import { LoginUseCase, LogoutUseCase, RestoreSessionUseCase } from '../Application/Identity/AuthUseCases'
import { GetMyStatisticsUseCase, SubmitNotebookAnswerUseCase } from '../Application/Performance/PerformanceUseCases'
import { ListPublishedQuestionsUseCase } from '../Application/QuestionBank/QuestionUseCases'
import { CreateNotebookUseCase, FinishNotebookUseCase, GetNotebookStatisticsUseCase, GetNotebookUseCase, ListNotebookQuestionsUseCase, ListNotebooksUseCase, PauseNotebookUseCase, StartNotebookUseCase } from '../Application/Study/StudyUseCases'
import { configureAccessTokenProvider } from './Http/AxiosApiClient'
import { AxiosAuthRepository } from './Identity/AxiosAuthRepository'
import { CatalogUseCases } from '../Application/Catalog/CatalogUseCases'
import { AxiosCatalogRepository } from './Catalog/AxiosCatalogRepository'
import { ImportUseCases } from '../Application/Import/ImportUseCases'
import { AxiosImportRepository } from './Import/AxiosImportRepository'
import { BrowserSessionStore } from './Identity/BrowserSessionStore'
import { AxiosPerformanceRepository } from './Performance/AxiosPerformanceRepository'
import { AxiosQuestionRepository } from './QuestionBank/AxiosQuestionRepository'
import { AxiosStudyRepository } from './Study/AxiosStudyRepository'
import { AxiosTaxonomyRepository } from './Taxonomy/AxiosTaxonomyRepository'
import { CreateTaxonomySubjectAliasUseCase, CreateTaxonomySubjectUseCase, ListTaxonomySubjectsUseCase, ListTaxonomyDuplicateSuggestionsUseCase, UpdateTaxonomySubjectUseCase } from '../Application/Taxonomy/TaxonomyUseCases'

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
  start: new StartNotebookUseCase(studyRepository),
  pause: new PauseNotebookUseCase(studyRepository),
  statistics: new GetNotebookStatisticsUseCase(studyRepository),
  finish: new FinishNotebookUseCase(studyRepository),
}
const taxonomyRepository = new AxiosTaxonomyRepository()
export const taxonomyUseCases = { list: new ListTaxonomySubjectsUseCase(taxonomyRepository), duplicateSuggestions: new ListTaxonomyDuplicateSuggestionsUseCase(taxonomyRepository), create: new CreateTaxonomySubjectUseCase(taxonomyRepository), createAlias: new CreateTaxonomySubjectAliasUseCase(taxonomyRepository), update: new UpdateTaxonomySubjectUseCase(taxonomyRepository) }
export const questionUseCases = { listPublished: new ListPublishedQuestionsUseCase(new AxiosQuestionRepository()) }
const performanceRepository = new AxiosPerformanceRepository()
export const performanceUseCases = { getMine: new GetMyStatisticsUseCase(performanceRepository), submitAnswer: new SubmitNotebookAnswerUseCase(performanceRepository) }
export const catalogUseCases = new CatalogUseCases(new AxiosCatalogRepository())
export const importUseCases = new ImportUseCases(new AxiosImportRepository())
