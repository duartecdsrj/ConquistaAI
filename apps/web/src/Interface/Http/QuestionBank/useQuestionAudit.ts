import { readonly, ref } from 'vue'
import type { QuestionAuditFindingPage, QuestionAuditReport } from '../../../Domain/QuestionBank/QuestionAuditRepository'
import { questionAuditUseCases } from '../../../Infrastructure/Container'
export function useQuestionAudit() {
  const loading = ref(false); const error = ref(''); const report = ref<QuestionAuditReport | null>(null); const findings = ref<QuestionAuditFindingPage | null>(null)
  async function load(page = 1): Promise<void> { loading.value = true; error.value = ''; try { const [latest, items] = await Promise.all([questionAuditUseCases.latest.execute(), questionAuditUseCases.findings.execute({ page, perPage: 25 })]); report.value = latest; findings.value = items } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar a auditoria.' } finally { loading.value = false } }
  async function goTo(page: number): Promise<void> { if (!findings.value || page < 1 || page > findings.value.totalPages) return; await load(page) }
  return { error: readonly(error), findings: readonly(findings), goTo, load, loading: readonly(loading), report: readonly(report) }
}
