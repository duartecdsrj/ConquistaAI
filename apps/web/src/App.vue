<template>
  <q-inner-loading :showing="loading" label="Carregando sessão..." />
  <LoginPage v-if="!loading && !authenticated" :submitting="submitting" :error="error" :pending-approval="pendingApproval" :google-enabled="googleEnabled" @submit="login" @google-ready="initializeGoogleButton" />
  <NotebookExecutionPage v-else-if="!loading && user && activeNotebookId" :notebook-id="activeNotebookId" :can-manage="user.roles.includes('ADMIN')" :question-content-version="questionContentVersion" @exit="closeNotebook" />
  <AppShell v-else-if="!loading && user" :user="user" :active="section" :can-manage="user.roles.includes('ADMIN')" @navigate="section = $event" @logout="logout">
    <ProfilePage v-if="section === 'profile'" :name="user.name" />
    <HomePage v-else-if="section === 'home'" :user="user" @navigate="section = $event" />
    <NotebooksPage v-else-if="section === 'notebooks'" @open="openNotebook" @performance="openPerformance" />
    <QuestionsPage v-else-if="section === 'questions'" />
    <CatalogPage v-else-if="section === 'catalog'" />
    <ImportPage v-else-if="section === 'import'" />
    <EditorialPage v-else-if="section === 'editorial'" />
    <TaxonomyPage v-else-if="section === 'taxonomy'" />
    <QuestionAuditPage v-else-if="section === 'audit'" />
    <UserManagementPage v-else-if="section === 'users'" />
    <AssistantPage v-else-if="section === 'assistant'" />
    <DiscoveryPage v-else-if="section === 'discovery'" />
    <ArenaPage v-else-if="section === 'arena'" :user-id="user.id" />
    <PerformancePage v-else :initial-exam-id="selectedPerformanceExamId" />
  </AppShell>
  <q-banner v-if="activeNotification" class="global-correction-notification" rounded inline-actions>
    <template #avatar><q-icon :name="activeNotification.status === 'PROPOSED' ? 'task_alt' : 'error_outline'" :color="activeNotification.status === 'PROPOSED' ? 'positive' : 'negative'" /></template>
    {{ activeNotification.status === 'PROPOSED' ? 'A proposta de correção está pronta para revisão.' : 'Não foi possível criar uma proposta segura para a correção solicitada.' }}
    <template #action><q-btn flat no-caps label="Ver resultado" @click="openNotification" /><q-btn flat no-caps label="Ignorar" @click="ignoreNotification" /><q-btn flat round icon="close" aria-label="Fechar" @click="dismissNotification" /></template>
  </q-banner>
  <q-dialog v-model="correctionDialog"><q-card class="notification-dialog preview-dialog"><q-card-section><p class="eyebrow">CORREÇÃO DE QUESTÃO</p><h2>{{ correctionRequest?.status === "PROPOSED" ? "Proposta pronta" : "Resultado da solicitação" }}</h2><q-banner v-if="correctionRequest?.proposal" rounded class="success-banner">{{ correctionRequest.proposal.summary }}</q-banner><QuestionCorrectionProposalPreview v-if="correctionRequest?.proposal" :proposal="correctionRequest.proposal" :request-id="correctionRequest.id"/><q-input v-if="canResendCorrection" v-model="correctionSuggestions" outlined autogrow label="Sugestões adicionais (opcional)" hint="O Codex localizará automaticamente a figura nas páginas de evidência; use este campo apenas para contexto adicional." class="q-mt-md"/><q-banner v-if="!correctionRequest?.proposal" rounded class="error-banner">{{ correctionRequest?.errorMessage || "A proposta não pôde ser gerada com segurança." }}</q-banner><q-banner v-if="correctionError" rounded class="error-banner q-mt-sm">{{ correctionError }}</q-banner></q-card-section><q-card-actions align="right"><q-btn flat no-caps label="Fechar" @click="correctionDialog=false"/><q-btn v-if="canResendCorrection" outline no-caps color="primary"  :label="correctionRequest?.status === 'FAILED' ? 'Reenviar análise' : 'Reenviar com sugestões'" :loading="correctionLoading" :disable="correctionLoading" @click="resendProposal"/><q-btn v-if="canApproveProposal" unelevated no-caps color="positive" label="Aprovar proposta" :loading="correctionLoading" @click="approveProposal"/></q-card-actions></q-card></q-dialog>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import HomePage from './Interface/Http/Home/HomePage.vue'
import LoginPage from './Interface/Http/Identity/LoginPage.vue'
import AppShell, { type ApplicationSection } from './Interface/Http/Layout/AppShell.vue'
import NotebooksPage from './Interface/Http/Study/NotebooksPage.vue'
import NotebookExecutionPage from './Interface/Http/Study/NotebookExecutionPage.vue'
import QuestionsPage from './Interface/Http/QuestionBank/QuestionsPage.vue'
import PerformancePage from './Interface/Http/Performance/PerformancePage.vue'
import AssistantPage from './Interface/Http/Assistant/AssistantPage.vue'
import CatalogPage from './Interface/Http/Catalog/CatalogPage.vue'
import ImportPage from './Interface/Http/Import/ImportPage.vue'
import DiscoveryPage from './Interface/Http/Discovery/DiscoveryPage.vue'
import EditorialPage from './Interface/Http/Editorial/EditorialPage.vue'
import TaxonomyPage from './Interface/Http/Taxonomy/TaxonomyPage.vue'
import QuestionAuditPage from './Interface/Http/QuestionBank/QuestionAuditPage.vue'
import UserManagementPage from './Interface/Http/UserManagement/UserManagementPage.vue'
import ProfilePage from './Interface/Http/Profile/ProfilePage.vue'
import ArenaPage from './Interface/Http/Arena/ArenaPage.vue'
import QuestionCorrectionProposalPreview from './Interface/Http/QuestionBank/QuestionCorrectionProposalPreview.vue'
import { useAuth } from './Interface/Http/Identity/useAuth'
import { useQuestionCorrection } from './Interface/Http/QuestionBank/useQuestionCorrection'
import { useQuestionCorrectionNotifications } from './Interface/Http/Realtime/useQuestionCorrectionNotifications'
import { questionUseCases } from './Infrastructure/Container'

const section = ref<ApplicationSection>('home')
const activeNotebookId = ref<string | null>(null)
const selectedPerformanceExamId = ref<string | null>(null)
const correctionDialog = ref(false)
const correctionSuggestions = ref("")
const questionContentVersion = ref(0)
const { authenticated, error, googleEnabled, initializeGoogleButton, loading, login, logout, pendingApproval, restore, submitting, user } = useAuth()
const { approve, error: correctionError, loading: correctionLoading, refresh, request: correctionRequest, submit } = useQuestionCorrection()
const { connect, dismiss, ignore, isIgnored, event: notification } = useQuestionCorrectionNotifications()
const pendingNotification = ref<import("./Infrastructure/Realtime/QuestionCorrectionRealtimeClient").QuestionCorrectionRealtimeEvent | null>(null)
const activeNotification = computed(() => notification.value ?? pendingNotification.value)
const canApproveProposal = computed(() => user.value?.roles.includes("ADMIN") === true && correctionRequest.value?.status === "PROPOSED")
const canResendCorrection = computed(() => user.value?.roles.includes("ADMIN") === true && (correctionRequest.value?.status === "PROPOSED" || correctionRequest.value?.status === "FAILED"))
async function recoverNotification(): Promise<void> { try { const completed = await questionUseCases.latestCompletedCorrection.execute(); if ((completed.status === "PROPOSED" || completed.status === "FAILED") && !isIgnored(completed.id)) pendingNotification.value = { userId: "", requestId: completed.id, questionId: completed.questionId, status: completed.status } } catch { } }
function dismissNotification(): void { pendingNotification.value = null; dismiss() }
function ignoreNotification(): void { const active = activeNotification.value; if (!active) return; ignore(active.requestId); pendingNotification.value = null; dismiss() }
const validSections: readonly ApplicationSection[] = ['profile', 'home', 'notebooks', 'questions', 'catalog', 'import', 'editorial', 'taxonomy', 'audit', 'assistant', 'discovery', 'users', 'arena', 'performance']

function openNotebook(id: string): void { activeNotebookId.value = id }
function openPerformance(examId: string): void { selectedPerformanceExamId.value = examId; section.value = 'performance' }
function closeNotebook(): void { activeNotebookId.value = null; section.value = 'notebooks' }
async function openNotification(): Promise<void> { if (!activeNotification.value) return; await refresh(activeNotification.value.questionId); correctionDialog.value = true; dismissNotification() }
async function approveProposal(): Promise<void> { if (await approve()) { dismissNotification(); questionContentVersion.value += 1; correctionDialog.value = false } }
async function resendProposal(): Promise<void> { const request = correctionRequest.value; const suggestions = correctionSuggestions.value.trim(); if (!request) return; const instruction = suggestions ? "Solicitação anterior: " + request.instruction + String.fromCharCode(10, 10) + "Sugestões para a nova análise: " + suggestions : request.instruction; if (await submit(request.questionId, instruction)) correctionSuggestions.value = "" }
function restoreLocation(): void { const params = new URLSearchParams(window.location.search); const saved = params.get('section'); if (saved && validSections.includes(saved as ApplicationSection)) section.value = saved as ApplicationSection; const notebook = params.get('notebook'); if (notebook) activeNotebookId.value = notebook }
watch([section, activeNotebookId], () => { const url = new URL(window.location.href); url.searchParams.set('section', section.value); if (activeNotebookId.value) url.searchParams.set('notebook', activeNotebookId.value); else url.searchParams.delete('notebook'); window.history.replaceState(null, '', url) })
onMounted(async () => { await restore(); if (user.value) { restoreLocation(); connect(); await recoverNotification() } })
</script>
<style scoped>
.global-correction-notification{position:fixed;right:22px;bottom:22px;z-index:7000;max-width:530px;background:#fff;box-shadow:0 14px 38px rgba(20,41,80,.22)}.notification-dialog{min-width:min(520px,94vw)}.preview-dialog{width:min(760px,calc(100vw - 32px));max-height:90vh;overflow:auto}.eyebrow{margin:0 0 8px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.notification-dialog h2{margin:0 0 14px;color:#142950}.success-banner{background:#edfff4;color:#17633b}.error-banner{background:#fff3f2;color:#a62922}
</style>
