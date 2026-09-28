import { ListDiscoveryResourcesUseCase, SearchDiscoveryUseCase } from '../Application/Discovery/DiscoveryUseCases'
import { AxiosDiscoveryRepository } from './Discovery/AxiosDiscoveryRepository'
import { ProfileAvatarUseCases } from '../Application/Profile/ProfileAvatarUseCases'
import { AxiosProfileAvatarRepository } from './Profile/AxiosProfileAvatarRepository'
import { UserManagementUseCases } from '../Application/UserManagement/UserManagementUseCases'
import { AxiosUserManagementRepository } from './UserManagement/AxiosUserManagementRepository'
import { CreateAssistantConversationUseCase, ListAssistantConversationsUseCase, ListAssistantMessagesUseCase, ListAssistantSyllabiUseCase, SendAssistantMessageUseCase } from '../Application/Assistant/AssistantUseCases'
import { AxiosAssistantRepository } from './Assistant/AxiosAssistantRepository'
import { GoogleLoginUseCase, LoginUseCase, LogoutUseCase, RestoreSessionUseCase } from '../Application/Identity/AuthUseCases'
import { GetMyStatisticsUseCase, GetSyllabusDashboardUseCase, SubmitNotebookAnswerUseCase } from '../Application/Performance/PerformanceUseCases'
import { ApproveQuestionCorrectionUseCase, AskQuestionPdfAssistanceUseCase, GetLatestQuestionCorrectionUseCase, GetLatestCompletedQuestionCorrectionUseCase, ListPublishedQuestionsUseCase, RequestQuestionCorrectionUseCase } from '../Application/QuestionBank/QuestionUseCases'
import { GetLatestQuestionAuditReportUseCase, ListLatestQuestionAuditFindingsUseCase } from '../Application/QuestionBank/QuestionAuditUseCases'
import { CreateNotebookUseCase, CreateDirectedStudyPlanUseCase, FinishNotebookUseCase, ListDirectedStudyPlansUseCase, ListPositionSubjectsUseCase, GetNotebookStatisticsUseCase, GetNotebookUseCase, GetStudyGoalUseCase, GetStudyPlanUseCase, ListNotebookQuestionsUseCase, ListNotebooksUseCase, PauseNotebookUseCase, SetActiveNotebookQuestionUseCase, StartNotebookUseCase, UpdateStudyGoalUseCase } from '../Application/Study/StudyUseCases'
import { configureAccessTokenProvider, configureRefreshHandler } from './Http/AxiosApiClient'
import { AxiosAuthRepository } from './Identity/AxiosAuthRepository'
import { GoogleIdentityServices } from './Identity/GoogleIdentityServices'
import { CatalogUseCases } from '../Application/Catalog/CatalogUseCases'
import { AxiosCatalogRepository } from './Catalog/AxiosCatalogRepository'
import { ImportUseCases } from '../Application/Import/ImportUseCases'
import { AxiosImportRepository } from './Import/AxiosImportRepository'
import { BrowserSessionStore } from './Identity/BrowserSessionStore'
import { AxiosPerformanceRepository } from './Performance/AxiosPerformanceRepository'
import { AxiosQuestionRepository } from './QuestionBank/AxiosQuestionRepository'
import { AxiosQuestionAuditRepository } from './QuestionBank/AxiosQuestionAuditRepository'
import { AxiosStudyRepository } from './Study/AxiosStudyRepository'
import { AxiosTaxonomyRepository } from './Taxonomy/AxiosTaxonomyRepository'
import { AxiosEditorialRepository } from './Editorial/AxiosEditorialRepository'
import { AssignEditorialQuestionTaxonomyUseCase, ListDraftQuestionsUseCase, MarkEditorialQuestionsForApprovalUseCase, PublishEditorialQuestionUseCase, PublishEditorialQuestionsUseCase } from '../Application/Editorial/EditorialUseCases'
import { CreateTaxonomySubjectAliasUseCase, CreateTaxonomySubjectUseCase, MergeTaxonomySubjectsUseCase, ListTaxonomyReconciliationProposalsUseCase, ListTaxonomySubjectsUseCase, ListTaxonomyDuplicateSuggestionsUseCase, UpdateTaxonomySubjectUseCase } from '../Application/Taxonomy/TaxonomyUseCases'

const sessionStore = new BrowserSessionStore()
configureAccessTokenProvider(() => sessionStore.accessToken())
const authRepository = new AxiosAuthRepository()
export const googleIdentityServices = new GoogleIdentityServices(import.meta.env.VITE_GOOGLE_CLIENT_ID ?? '')
configureRefreshHandler(async () => { try { const session = await authRepository.refresh(); sessionStore.save(session); return session.accessToken } catch { sessionStore.clear(); return null } })
const discoveryRepository = new AxiosDiscoveryRepository()
export const profileAvatarUseCases = new ProfileAvatarUseCases(new AxiosProfileAvatarRepository())
export const userManagementUseCases = new UserManagementUseCases(new AxiosUserManagementRepository())
export const discoveryUseCases = { list: new ListDiscoveryResourcesUseCase(discoveryRepository), search: new SearchDiscoveryUseCase(discoveryRepository) }
const assistantRepository = new AxiosAssistantRepository()
export const assistantUseCases = { syllabi: new ListAssistantSyllabiUseCase(assistantRepository), conversations: new ListAssistantConversationsUseCase(assistantRepository), create: new CreateAssistantConversationUseCase(assistantRepository), messages: new ListAssistantMessagesUseCase(assistantRepository), send: new SendAssistantMessageUseCase(assistantRepository) }

const studyRepository = new AxiosStudyRepository()
export const identityUseCases = {
  login: new LoginUseCase(authRepository, sessionStore),
  googleLogin: new GoogleLoginUseCase(authRepository, sessionStore),
  logout: new LogoutUseCase(authRepository, sessionStore),
  restoreSession: new RestoreSessionUseCase(authRepository, sessionStore),
}
export const studyUseCases = {
  list: new ListNotebooksUseCase(studyRepository),
  directedPlans: new ListDirectedStudyPlansUseCase(studyRepository),
  createDirectedPlan: new CreateDirectedStudyPlanUseCase(studyRepository),
  positionSubjects: new ListPositionSubjectsUseCase(studyRepository),
  get: new GetNotebookUseCase(studyRepository),
  listQuestions: new ListNotebookQuestionsUseCase(studyRepository),
  create: new CreateNotebookUseCase(studyRepository),
  start: new StartNotebookUseCase(studyRepository),
  pause: new PauseNotebookUseCase(studyRepository),
  setActiveQuestion: new SetActiveNotebookQuestionUseCase(studyRepository),
  statistics: new GetNotebookStatisticsUseCase(studyRepository),
  finish: new FinishNotebookUseCase(studyRepository),
  plan: new GetStudyPlanUseCase(studyRepository),
  goal: new GetStudyGoalUseCase(studyRepository),
  updateGoal: new UpdateStudyGoalUseCase(studyRepository),
}
const taxonomyRepository = new AxiosTaxonomyRepository()
export const taxonomyUseCases = { list: new ListTaxonomySubjectsUseCase(taxonomyRepository), duplicateSuggestions: new ListTaxonomyDuplicateSuggestionsUseCase(taxonomyRepository), create: new CreateTaxonomySubjectUseCase(taxonomyRepository), createAlias: new CreateTaxonomySubjectAliasUseCase(taxonomyRepository), merge: new MergeTaxonomySubjectsUseCase(taxonomyRepository), reconciliationProposals: new ListTaxonomyReconciliationProposalsUseCase(taxonomyRepository), update: new UpdateTaxonomySubjectUseCase(taxonomyRepository) }
const questionRepository = new AxiosQuestionRepository()
export const questionUseCases = { listPublished: new ListPublishedQuestionsUseCase(questionRepository), askPdfAssistance: new AskQuestionPdfAssistanceUseCase(questionRepository), requestCorrection: new RequestQuestionCorrectionUseCase(questionRepository), latestCorrection: new GetLatestQuestionCorrectionUseCase(questionRepository), latestCompletedCorrection: new GetLatestCompletedQuestionCorrectionUseCase(questionRepository), approveCorrection: new ApproveQuestionCorrectionUseCase(questionRepository) }
const questionAuditRepository = new AxiosQuestionAuditRepository()
export const questionAuditUseCases = { latest: new GetLatestQuestionAuditReportUseCase(questionAuditRepository), findings: new ListLatestQuestionAuditFindingsUseCase(questionAuditRepository) }
const performanceRepository = new AxiosPerformanceRepository()
export const performanceUseCases = { getMine: new GetMyStatisticsUseCase(performanceRepository), getDashboard: new GetSyllabusDashboardUseCase(performanceRepository), submitAnswer: new SubmitNotebookAnswerUseCase(performanceRepository) }
export const catalogUseCases = new CatalogUseCases(new AxiosCatalogRepository())
export const importUseCases = new ImportUseCases(new AxiosImportRepository())
const editorialRepository = new AxiosEditorialRepository()
export const editorialUseCases = { listDrafts: new ListDraftQuestionsUseCase(editorialRepository), publish: new PublishEditorialQuestionUseCase(editorialRepository), assignTaxonomy: new AssignEditorialQuestionTaxonomyUseCase(editorialRepository), markForApproval: new MarkEditorialQuestionsForApprovalUseCase(editorialRepository), publishMany: new PublishEditorialQuestionsUseCase(editorialRepository) }

import { ArenaUseCases } from "../Application/Arena/ArenaUseCases"
import { AxiosArenaRepository } from "./Arena/AxiosArenaRepository"
export const arenaUseCases = new ArenaUseCases(new AxiosArenaRepository())
