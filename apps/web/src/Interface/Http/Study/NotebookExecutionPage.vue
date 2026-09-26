<template>
  <q-layout view="hHh Lpr fFf" class="execution-layout">
    <q-header class="execution-header">
      <q-toolbar class="execution-toolbar">
        <q-btn flat round dense icon="arrow_back" aria-label="Voltar aos cadernos" @click="emit('exit')" />
        <div class="execution-brand"><q-img src="/images/concursos-study-mark.png" width="31px" height="31px" fit="contain" /><strong class="gt-xs">ConquistaAI</strong></div>
        <div class="book-context gt-sm"><span>Cadernos e plano</span><q-icon name="chevron_right" size="16px" /><span>Resolver caderno</span></div>
        <q-space />
        <q-btn outline no-caps color="negative" icon="flag" label="Finalizar" class="finish-button" aria-label="Finalizar caderno" @click="confirmFinish = true" />
        <q-btn flat no-caps class="pause-button" :aria-label="notebook?.status === 'IN_PROGRESS' ? 'Pausar contador' : 'Retomar contador'" :icon="notebook?.status === 'IN_PROGRESS' ? 'pause' : 'play_arrow'" :label="notebook?.status === 'IN_PROGRESS' ? 'Pausar' : 'Retomar'" :loading="pausing" @click="togglePause" />
        <div class="timer"><q-icon name="timer" size="17px" /><span>{{ elapsed }}</span></div>
        <div class="header-progress gt-sm"><span>{{ statistics.answered }} / {{ totalQuestions }}</span><q-linear-progress rounded size="7px" color="primary" track-color="blue-1" :value="progress" /></div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="execution-page">
        <q-inner-loading :showing="loading" label="Abrindo caderno..." color="primary" />
        <q-banner v-if="error" rounded class="error-banner">{{ error }}<template #action><q-btn flat no-caps label="Voltar" @click="emit('exit')" /></template></q-banner>

        <template v-else-if="notebook && currentQuestion">
          <section class="book-heading">
            <div><p class="eyebrow">{{ notebook.mode === 'STUDY' ? 'ESTUDO GUIADO' : 'SIMULADO' }}</p><h1>{{ notebook.name }}</h1><p>Questão {{ currentIndex + 1 }} de {{ totalQuestions }}</p></div>
            <q-btn flat dense no-caps icon="bookmark_border" label="Marcar" class="gt-xs" />
          </section>

          <div class="execution-grid">
            <aside class="question-nav-panel">
              <q-tabs v-model="navTab" dense align="justify" active-color="primary" indicator-color="primary"><q-tab name="questions" icon="quiz" label="Questões" /><q-tab name="summary" icon="insights" label="Resumo" /></q-tabs>
              <q-tab-panels v-model="navTab" animated class="nav-panels">
                <q-tab-panel name="questions">
                  <q-select v-model="questionFilter" dense outlined :options="filterOptions" emit-value map-options label="Exibir" class="question-filter" />
                  <div class="legend"><span><i class="answered-dot" /> Respondida</span><span><i class="current-dot" /> Atual</span><span><i class="pending-dot" /> Pendente</span></div>
                  <div class="question-indexes"><q-btn v-for="(_, index) in visibleQuestions" :key="questions.indexOf(_)" flat no-caps :class="['question-index', { current: currentIndex === questions.indexOf(_), answered: statistics.answeredQuestionIds.includes(_.id) }]" :label="String(questions.indexOf(_) + 1)" @click="goTo(questions.indexOf(_))" /></div>
                </q-tab-panel>
                <q-tab-panel name="summary"><div class="nav-summary"><strong>{{ statistics.answered }} de {{ totalQuestions }}</strong><span>questões respondidas</span><q-linear-progress rounded size="8px" color="primary" track-color="blue-1" :value="progress" /><q-btn flat no-caps color="primary" icon="arrow_forward" label="Ir para a questão atual" @click="navTab = 'questions'" /></div></q-tab-panel>
              </q-tab-panels>
            </aside>

            <main class="question-workspace">
              <div class="question-meta"><q-btn flat round dense icon="arrow_back" aria-label="Questão anterior" :disable="currentIndex === 0" @click="previous" /><strong>Questão {{ currentIndex + 1 }}</strong><q-badge rounded color="amber-2" text-color="amber-10">{{ difficultyLabel }}</q-badge><q-chip v-if="currentQuestion.board" dense color="blue-1" text-color="primary">{{ currentQuestion.board }}{{ currentQuestion.year ? ' · ' + currentQuestion.year : '' }}</q-chip></div>
              <div v-if="currentQuestion.assetUrls?.length" class="question-assets"><QuestionAssetImage v-for="url in currentQuestion.assetUrls" :key="url" :url="url"/></div><div class="question-statement"><QuestionContent :value="currentQuestion.statement"/></div><div class="question-options" role="radiogroup" aria-label="Alternativas"><button v-for="option in currentQuestion.options" :key="option.id" type="button" :class="['option-card',{selected:selectedOptions[currentQuestion.id]===option.id}]" :disabled="isCurrentAnswered" @click="selectOption(option.id)"><q-icon :name="selectedOptions[currentQuestion.id]===option.id?'radio_button_checked':'radio_button_unchecked'"/><strong>{{option.label}}.</strong><QuestionContent :value="option.content"/><div v-if="option.assetUrls?.length" class="option-assets"><QuestionAssetImage v-for="url in option.assetUrls" :key="url" :url="url"/></div></button></div>
              <q-btn flat no-caps color="primary" icon="auto_awesome" label="Tirar dúvida com a fonte" class="q-mt-sm" @click="openPdfHelp" /><q-banner v-if="notice" rounded class="success-banner"><template #avatar><q-icon name="check_circle" /></template>{{ notice }}</q-banner>
              <section v-if="isCurrentAnswered" class="answer-info"><q-icon name="info" color="primary" /><div><strong>Resposta registrada</strong><span>Continue para a próxima questão ou revise sua navegação.</span></div></section>
              <footer class="execution-actions"><q-btn outline no-caps icon="arrow_back" label="Anterior" :disable="currentIndex === 0 || savingAnswer" @click="previous" /><q-space /><q-btn v-if="!isCurrentAnswered" unelevated no-caps color="primary" icon-right="check" label="Confirmar resposta" :loading="savingAnswer" @click="submitCurrent" /><q-btn v-else unelevated no-caps color="primary" icon-right="arrow_forward" label="Próxima" :disable="currentIndex + 1 >= totalQuestions || savingAnswer" @click="next" /></footer>
            </main>

            <aside class="progress-panel">
              <div class="progress-panel-head"><div><p class="eyebrow">SEU PROGRESSO</p><h2>{{ notebook.name }}</h2></div><q-btn flat round dense icon="close" aria-label="Voltar aos cadernos" @click="emit('exit')" /></div>
              <p class="progress-caption">{{ currentIndex + 1 }} de {{ totalQuestions }} questões</p><q-linear-progress rounded size="8px" color="primary" track-color="blue-1" :value="progress" />
              <q-list class="progress-list"><q-item><q-item-section avatar><q-icon name="check_circle" color="positive" /></q-item-section><q-item-section>Respondidas</q-item-section><q-item-section side>{{ statistics.answered }}</q-item-section></q-item><q-item><q-item-section avatar><q-icon name="radio_button_unchecked" color="grey-5" /></q-item-section><q-item-section>Não respondidas</q-item-section><q-item-section side>{{ totalQuestions - statistics.answered }}</q-item-section></q-item><q-item><q-item-section avatar><q-icon name="check_circle" color="positive" /></q-item-section><q-item-section>Acertos</q-item-section><q-item-section side>{{ statistics.correct }} ({{ statistics.percentage }}%)</q-item-section></q-item><q-item><q-item-section avatar><q-icon name="cancel" color="negative" /></q-item-section><q-item-section>Erros</q-item-section><q-item-section side>{{ statistics.incorrect }}</q-item-section></q-item></q-list>
              <q-btn outline no-caps color="primary" icon="insights" label="Ver estatísticas detalhadas" class="full-width q-mt-md" />
              <q-btn outline no-caps color="negative" icon="flag" label="Finalizar caderno" class="full-width q-mt-sm" @click="confirmFinish = true" />
            </aside>
          </div>
        </template>

        <q-card v-else-if="!loading && !error" flat class="empty-execution"><q-card-section><q-icon name="quiz" size="42px" color="primary" /><h1>Este caderno não possui questões disponíveis.</h1><q-btn unelevated no-caps color="primary" label="Voltar aos cadernos" @click="emit('exit')" /></q-card-section></q-card>
      </q-page>
    </q-page-container>

    <q-dialog v-model="pdfHelpDialog"><q-card class="finish-dialog"><q-card-section><p class="eyebrow">FONTE DA QUESTÃO</p><h2>Tire sua dúvida com o PDF</h2><p>A resposta usa apenas as páginas vinculadas à questão importada.</p><q-input v-model="pdfDoubt" outlined autogrow label="Qual é sua dúvida?" class="q-mt-md" :disable="pdfHelpLoading"/><q-banner v-if="pdfHelpError" rounded class="error-banner q-mt-sm">{{ pdfHelpError }}</q-banner><q-card v-if="pdfHelpResponse" flat bordered class="q-mt-md"><q-card-section><QuestionContent :value="pdfHelpResponse.content"/><div class="q-mt-sm"><q-chip v-for="page in pdfHelpResponse.pages" :key="page" dense color="blue-1" text-color="primary">Página {{ page }}</q-chip></div></q-card-section></q-card></q-card-section><q-card-actions align="right"><q-btn flat no-caps label="Fechar" @click="pdfHelpDialog=false"/><q-btn unelevated no-caps color="primary" label="Consultar fonte" :loading="pdfHelpLoading" :disable="pdfDoubt.trim().length<3" @click="askPdfHelp"/></q-card-actions></q-card></q-dialog>

    <q-dialog v-model="confirmFinish" persistent><q-card class="finish-dialog"><q-card-section><p class="eyebrow">ENCERRAR SESSÃO</p><h2>Finalizar caderno?</h2><p>Você respondeu {{ statistics.answered }} de {{ totalQuestions }} questões. Depois de finalizar não será possível registrar novas respostas.</p></q-card-section><q-card-actions align="right"><q-btn flat no-caps label="Continuar estudando" @click="confirmFinish = false" /><q-btn unelevated no-caps color="negative" label="Finalizar" :loading="finishing" @click="complete" /></q-card-actions></q-card></q-dialog>
  </q-layout>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import QuestionContent from '../QuestionBank/QuestionContent.vue'
import QuestionAssetImage from '../QuestionBank/QuestionAssetImage.vue'
import { useNotebookExecution } from './useNotebookExecution'
import { useQuestionPdfAssistance } from '../QuestionBank/useQuestionPdfAssistance'

const props = defineProps<{ readonly notebookId: string }>()
const emit = defineEmits<{ exit: [] }>()
const confirmFinish = ref(false)
const pdfHelpDialog = ref(false); const pdfDoubt=ref(''); const { ask:askPdf, error:pdfHelpError, loading:pdfHelpLoading, response:pdfHelpResponse }=useQuestionPdfAssistance()
const navTab = ref('questions')
const questionFilter = ref<'ALL' | 'ANSWERED' | 'PENDING'>('ALL')
const filterOptions = [{ label: 'Todas', value: 'ALL' }, { label: 'Respondidas', value: 'ANSWERED' }, { label: 'Pendentes', value: 'PENDING' }]
const { currentIndex, currentQuestion, elapsed, error, finish, finishing, goTo, isCurrentAnswered, load, loading, next, notebook, notice, pausing, previous, progress, questions, savingAnswer, selectOption, selectedOptions, statistics, submitCurrent, togglePause, totalQuestions } = useNotebookExecution()
const visibleQuestions = computed(() => questionFilter.value === 'ANSWERED' ? questions.value.filter((question) => statistics.value.answeredQuestionIds.includes(question.id)) : questionFilter.value === 'PENDING' ? questions.value.filter((question) => !statistics.value.answeredQuestionIds.includes(question.id)) : questions.value)
const difficultyLabel = computed(() => currentQuestion.value?.difficulty === 'EASY' ? 'Fácil' : currentQuestion.value?.difficulty === 'HARD' ? 'Difícil' : 'Médio')
function openPdfHelp():void{pdfDoubt.value='';pdfHelpDialog.value=true}
async function askPdfHelp():Promise<void>{if(currentQuestion.value)await askPdf(currentQuestion.value.id,pdfDoubt.value)}
async function complete(): Promise<void> { if (await finish()) { confirmFinish.value = false; emit('exit') } }
onMounted(() => load(props.notebookId))
</script>

<style scoped>
.execution-layout{min-height:100vh;background:#f5f9ff;color:#10275b}.execution-header{background:#fff;color:#10275b;border-bottom:1px solid #e4edf9}.execution-toolbar{min-height:64px;max-width:1340px;margin:auto;padding:0 16px;gap:7px}.execution-brand{display:flex;align-items:center;gap:6px;font-size:13px}.book-context{display:flex;align-items:center;gap:3px;margin-left:20px;color:#6d83a6;font-size:11px}.timer{display:flex;align-items:center;gap:6px;padding:7px 10px;border-radius:8px;background:#f4f8ff;color:#10275b;font-size:13px;font-weight:800;font-variant-numeric:tabular-nums}.header-progress{width:170px;margin-left:12px}.header-progress span{display:block;margin-bottom:4px;color:#45618b;font-size:10px;text-align:right}.execution-page{max-width:1340px;margin:auto;padding:14px 16px 20px}.book-heading{display:flex;align-items:center;justify-content:space-between;margin:0 0 11px 204px}.eyebrow{margin:0 0 4px;color:#6682ad;font-size:10px;font-weight:800;letter-spacing:.06em}.book-heading h1{margin:0;color:#10275b;font-size:17px;line-height:1.25}.book-heading p:not(.eyebrow),.progress-caption{margin:3px 0 0;color:#71819e;font-size:11px}.execution-grid{display:grid;grid-template-columns:204px minmax(0,1fr) 248px;gap:12px}.question-nav-panel,.question-workspace,.progress-panel{border:1px solid #dfe9f6;border-radius:10px;background:#fff;box-shadow:0 5px 18px rgba(32,69,125,.035)}.question-nav-panel{min-height:620px}.nav-panels{background:transparent}.question-filter{margin-bottom:9px}.legend{display:grid;grid-template-columns:1fr 1fr;gap:7px;margin-bottom:12px;color:#71819e;font-size:9px}.legend span{display:flex;align-items:center;gap:4px}.legend i{width:7px;height:7px;border-radius:50%}.answered-dot{background:#22ba78}.current-dot{background:#1469f5}.pending-dot{border:1px solid #cbd8ea}.question-indexes{display:grid;grid-template-columns:repeat(5,1fr);gap:6px}.question-index{min-width:0!important;min-height:29px!important;padding:0!important;border:1px solid #edf2f8!important;border-radius:7px!important;background:#f8fbff!important;color:#183565!important;font-size:11px}.question-index.answered:after{content:'';position:absolute;right:4px;bottom:4px;width:4px;height:4px;border-radius:50%;background:#22ba78}.question-index.current{background:#1469f5!important;border-color:#1469f5!important;color:#fff!important}.question-workspace{min-height:620px;padding:14px}.question-meta{display:flex;align-items:center;gap:9px;color:#10275b;font-size:13px}.question-meta :deep(.q-chip){margin:0;font-size:10px}.question-assets{display:grid;gap:12px;margin:16px 0}.question-assets :deep(img){max-width:100%;max-height:520px;object-fit:contain;border:1px solid #dce6f3;border-radius:10px}.question-statement{margin:17px 0 14px;color:#10275b;font-size:15px;font-weight:600;line-height:1.48}.question-options{display:grid;gap:7px}.option-card{display:flex;align-items:flex-start;gap:10px;width:100%;min-height:42px;padding:10px 12px;border:1px solid #dce6f3;border-radius:7px;background:#fff;color:#344e77;text-align:left;cursor:pointer}.option-card:disabled{cursor:default}.option-card.selected{border-color:#5a96fb;background:#edf5ff}.option-card :deep(.content){flex:1;font-size:12px;line-height:1.36}.option-assets{width:100%}.option-assets :deep(img){max-width:100%;max-height:360px;object-fit:contain;border-radius:8px}.option-card :deep(.content p){margin:0}.success-banner{margin-top:10px;background:#e9fbf1;color:#12804e;font-size:12px}.answer-info{display:flex;align-items:center;gap:9px;margin-top:11px;padding:10px;border:1px solid #d9eafe;border-radius:8px;background:#f5f9ff;color:#35547f;font-size:11px}.answer-info strong,.answer-info span{display:block}.execution-actions{display:flex;align-items:center;gap:8px;margin-top:13px}.execution-actions .q-btn{min-height:34px;font-size:11px}.progress-panel{padding:14px}.progress-panel-head{display:flex;justify-content:space-between;gap:6px}.progress-panel h2{margin:0;color:#10275b;font-size:13px;line-height:1.3}.progress-list{margin-top:12px;border-top:1px solid #eef3fa;border-bottom:1px solid #eef3fa}.progress-list :deep(.q-item){min-height:35px;padding:5px 0;color:#496388;font-size:11px}.progress-list :deep(.q-item__section--avatar){min-width:23px}.progress-list :deep(.q-item__section--side){color:#193a6a;font-size:10px}.nav-summary{display:grid;gap:9px;place-items:start;padding:10px;color:#45618b;font-size:11px}.nav-summary strong{color:#10275b;font-size:16px}.error-banner{margin:auto;max-width:600px;background:#fff2f1;color:#a7332c}.empty-execution{margin:90px auto;max-width:550px;border-radius:18px;text-align:center}.empty-execution h1{font-size:20px;color:#1b345f}.finish-dialog{width:min(430px,calc(100vw - 32px));border-radius:18px}.finish-dialog h2{margin:0;color:#152c57}.finish-dialog p:not(.eyebrow){color:#687c9f;line-height:1.55}@media(max-width:900px){.execution-grid{grid-template-columns:180px minmax(0,1fr)}.progress-panel{display:none}.book-heading{margin-left:180px}.question-nav-panel{min-height:590px}}@media(max-width:600px){.execution-toolbar{min-height:57px;padding:0 8px}.execution-brand{margin-right:0}.finish-button :deep(.q-btn__content),.pause-button :deep(.q-btn__content){font-size:0}.finish-button,.pause-button{min-width:36px;padding:0}.timer{padding:6px;font-size:11px}.execution-page{padding:12px 10px 24px}.book-heading{margin:0 0 10px}.book-heading h1{font-size:15px}.execution-grid{display:flex;flex-direction:column;gap:9px}.question-nav-panel{order:2;min-height:auto}.question-workspace{order:1;min-height:auto;padding:12px}.question-workspace h2{margin:13px 0;font-size:14px}.question-indexes{grid-template-columns:repeat(10,1fr);gap:5px}.question-index{min-height:29px!important;font-size:10px}.question-options :deep(.q-radio){min-height:50px;padding:9px}.question-options :deep(.q-radio__label){font-size:11px}.execution-actions .q-btn{flex:1;min-width:0;padding:0 8px}.progress-panel{display:block;order:3}.question-nav-panel :deep(.q-tab-panel){padding:10px}.legend{grid-template-columns:repeat(3,1fr);font-size:8px}.header-progress{display:none}}
</style>