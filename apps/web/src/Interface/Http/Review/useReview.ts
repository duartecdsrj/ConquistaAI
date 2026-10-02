import { computed, ref } from 'vue'
import type { MasteryNode, NotebookAnalysis, ReviewSession } from '../../../Domain/Review/ReviewRepository'
import { reviewUseCases } from '../../../Infrastructure/Container'
export function useReview() {
  const loading = ref(false); const error = ref(''); const mastery = ref<readonly MasteryNode[]>([]); const total = ref(0); const analysis = ref<NotebookAnalysis | null>(null); const session = ref<ReviewSession | null>(null)
  const analysisProcessing = computed(() => analysis.value?.status === 'PENDING' || analysis.value?.status === 'PROCESSING')
  async function loadMastery(page = 1): Promise<void> { loading.value = true; error.value = ''; try { const result = await reviewUseCases.mastery({ page, perPage: 25 }); mastery.value = result.items; total.value = result.pagination.total } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar o mapa de domínio.' } finally { loading.value = false } }
  async function loadAnalysis(notebookId: string): Promise<void> { loading.value = true; error.value = ''; try { analysis.value = await reviewUseCases.analysis(notebookId) } catch (reason) { error.value = reason instanceof Error ? reason.message : 'Não foi possível carregar a análise.' } finally { loading.value = false } }
  async function rate(sessionId:string,cardId:string,rating:'AGAIN'|'HARD'|'GOOD'|'EASY'):Promise<boolean>{loading.value=true;error.value='';try{session.value=(await reviewUseCases.rate(sessionId,cardId,rating)).session;return true}catch(reason){error.value=reason instanceof Error?reason.message:'Não foi possível registrar a revisão.';return false}finally{loading.value=false}}
  async function navigate(sessionId:string,direction:'NEXT'|'PREVIOUS'):Promise<boolean>{loading.value=true;error.value='';try{session.value=await reviewUseCases.navigate(sessionId,direction);return true}catch(reason){error.value=reason instanceof Error?reason.message:'Não foi possível navegar entre os cards.';return false}finally{loading.value=false}}
  async function loadSession(kind:'daily'|'quick'|'advance'='daily'):Promise<void>{loading.value=true;error.value='';try{session.value=kind==='daily'?await reviewUseCases.daily():kind==='quick'?await reviewUseCases.quick():await reviewUseCases.advance()}catch(reason){error.value=reason instanceof Error?reason.message:'Não foi possível carregar os cards de revisão.'}finally{loading.value=false}}
  return { analysis, analysisProcessing, error, loadAnalysis, loadMastery, loadSession, loading, mastery, navigate, rate, session, total }
}
