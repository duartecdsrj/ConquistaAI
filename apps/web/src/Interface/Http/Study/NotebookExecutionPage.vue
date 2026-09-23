<template>
  <q-layout view="hHh Lpr fFf" class="execution-layout">
    <q-header class="execution-header">
      <q-toolbar class="execution-toolbar">
        <q-btn flat round icon="arrow_back" aria-label="Voltar aos cadernos" @click="emit('exit')" />
        <q-img src="/images/concursos-study-mark.png" width="34px" height="34px" fit="contain" class="q-ml-sm" />
        <div class="q-ml-sm">
          <strong>ConquistaAI</strong>
          <span class="header-caption">Caderno em execução</span>
        </div>
        <q-space />
        <div class="timer"><q-icon name="timer" size="19px" /><span>{{ elapsed }}</span></div>
        <q-btn flat no-caps :icon="notebook?.status === 'IN_PROGRESS' ? 'pause' : 'play_arrow'" :label="notebook?.status === 'IN_PROGRESS' ? 'Pausar' : 'Retomar'" :loading="pausing" @click="togglePause" /><q-btn unelevated no-caps color="negative" label="Finalizar caderno" class="q-ml-md" @click="confirmFinish = true" />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="execution-page">
        <q-inner-loading :showing="loading" label="Abrindo caderno..." color="primary" />
        <q-banner v-if="error" rounded class="error-banner">
          {{ error }}
          <template #action><q-btn flat no-caps label="Voltar" @click="emit('exit')" /></template>
        </q-banner>

        <template v-else-if="notebook && currentQuestion">
          <section class="execution-summary">
            <div>
              <p class="eyebrow">{{ notebook.mode === 'STUDY' ? 'ESTUDO GUIADO' : 'SIMULADO' }}</p>
              <h1>{{ notebook.name }}</h1>
              <p>Questão {{ currentIndex + 1 }} de {{ totalQuestions }}</p>
            </div>
            <div class="answered-counter">{{ Math.round(progress * totalQuestions) }} respondidas</div>
          </section>

          <section class="stats-row"><q-card flat><q-card-section><span>Respondidas</span><strong>{{ statistics.answered }}/{{ statistics.total }}</strong></q-card-section></q-card><q-card flat><q-card-section><span>Acertos</span><strong>{{ statistics.correct }}</strong></q-card-section></q-card><q-card flat><q-card-section><span>Aproveitamento</span><strong>{{ statistics.percentage }}%</strong></q-card-section></q-card></section>
          <q-linear-progress rounded size="8px" color="primary" track-color="blue-1" :value="progress" class="progress-bar" />

          <main class="question-card">
            <div class="question-meta">
              <q-badge outline color="primary">{{ currentQuestion.difficulty === 'EASY' ? 'Fácil' : currentQuestion.difficulty === 'MEDIUM' ? 'Média' : 'Difícil' }}</q-badge>
              <span v-if="currentQuestion.board">{{ currentQuestion.board }}</span>
              <span v-if="currentQuestion.year">{{ currentQuestion.year }}</span>
            </div>
            <h2>{{ currentQuestion.statement }}</h2>

            <q-option-group
              :model-value="selectedOptions[currentQuestion.id]"
              :options="currentQuestion.options.map((option) => ({ label: option.label + '. ' + option.content, value: option.id }))"
              type="radio"
              color="primary"
              class="question-options"
              :disable="isCurrentAnswered"
              @update:model-value="selectOption"
            />

            <q-banner v-if="notice" rounded class="success-banner">{{ notice }}</q-banner>
          </main>

          <footer class="execution-actions">
            <q-btn flat no-caps icon="arrow_back" label="Anterior" :disable="currentIndex === 0 || savingAnswer" @click="previous" />
            <div class="action-spacer" />
            <q-btn v-if="!isCurrentAnswered" unelevated no-caps color="primary" icon-right="check" label="Confirmar resposta" :loading="savingAnswer" @click="submitCurrent" />
            <q-btn flat no-caps icon-right="arrow_forward" label="Próxima" :disable="currentIndex + 1 >= totalQuestions || savingAnswer" @click="next" />
          </footer>
        </template>

        <q-card v-else-if="!loading && !error" flat class="empty-execution">
          <q-card-section>
            <q-icon name="quiz" size="42px" color="primary" />
            <h1>Este caderno não possui questões disponíveis.</h1>
            <q-btn unelevated no-caps color="primary" label="Voltar aos cadernos" @click="emit('exit')" />
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>

    <q-dialog v-model="confirmFinish" persistent>
      <q-card class="finish-dialog">
        <q-card-section><p class="eyebrow">ENCERRAR SESSÃO</p><h2>Finalizar caderno?</h2><p>Você respondeu {{ Math.round(progress * totalQuestions) }} de {{ totalQuestions }} questões. Depois de finalizar não será possível registrar novas respostas.</p></q-card-section>
        <q-card-actions align="right"><q-btn flat no-caps label="Continuar estudando" @click="confirmFinish = false" /><q-btn unelevated no-caps color="negative" label="Finalizar" :loading="finishing" @click="complete" /></q-card-actions>
      </q-card>
    </q-dialog>
  </q-layout>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useNotebookExecution } from './useNotebookExecution'

const props = defineProps<{ readonly notebookId: string }>()
const emit = defineEmits<{ exit: [] }>()
const confirmFinish = ref(false)
const {
  currentIndex, currentQuestion, elapsed, error, finish, finishing, isCurrentAnswered, load, loading, notebook, next, notice, previous,
  progress, pausing, savingAnswer, selectOption, statistics, togglePause, selectedOptions, submitCurrent, totalQuestions,
} = useNotebookExecution()

async function complete(): Promise<void> {
  if (await finish()) {
    confirmFinish.value = false
    emit('exit')
  }
}

onMounted(() => load(props.notebookId))
</script>

<style scoped>
.execution-layout{min-height:100vh;background:#f4f8fe;color:#142950}.execution-header{background:#fff;color:#17305c;border-bottom:1px solid #e2ebf7}.execution-toolbar{min-height:68px;max-width:1180px;margin:auto;padding:0 26px}.header-caption{display:block;color:#7b8cac;font-size:11px}.timer{display:flex;align-items:center;gap:7px;padding:8px 12px;border-radius:10px;background:#edf4ff;color:#2467d4;font-weight:800;font-variant-numeric:tabular-nums}.execution-page{max-width:940px;margin:auto;padding:42px 28px 50px}.execution-summary{display:flex;justify-content:space-between;align-items:end;gap:20px}.eyebrow{margin:0 0 8px;color:#7287af;font-size:11px;font-weight:800;letter-spacing:.1em}.execution-summary h1{margin:0;color:#132c59;font-size:30px}.execution-summary p:not(.eyebrow){margin:8px 0 0;color:#7183a4}.answered-counter{padding:9px 12px;border-radius:10px;background:#e8f2ff;color:#2969cd;font-size:13px;font-weight:700}.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:20px 0}.stats-row .q-card{border:1px solid #e2ebf7;border-radius:12px}.stats-row span{display:block;color:#7183a4;font-size:12px}.stats-row strong{display:block;color:#173b73;font-size:20px;margin-top:5px}.progress-bar{margin:22px 0 24px}.question-card{padding:34px;border:1px solid #e2ebf7;border-radius:20px;background:#fff;box-shadow:0 12px 35px rgba(35,62,109,.06)}.question-meta{display:flex;gap:10px;align-items:center;color:#7183a4;font-size:13px}.question-card h2{margin:21px 0 27px;color:#142b56;font-size:22px;line-height:1.45}.question-options :deep(.q-radio){display:flex;align-items:flex-start;margin:0 0 12px;padding:15px;border:1px solid #e0e9f7;border-radius:12px;transition:.16s}.question-options :deep(.q-radio--truthy){border-color:#3d75dd;background:#f1f6ff}.question-options :deep(.q-radio__label){padding-left:10px;line-height:1.45;color:#2d436b}.success-banner{margin-top:22px;background:#e8f8ee;color:#167044}.execution-actions{display:flex;align-items:center;gap:8px;margin-top:24px}.action-spacer{flex:1}.execution-actions .q-btn{border-radius:10px}.finish-dialog{width:min(430px,calc(100vw - 32px));border-radius:18px}.finish-dialog h2{margin:0;color:#152c57}.finish-dialog p:not(.eyebrow){color:#687c9f;line-height:1.55}.error-banner{margin:auto;max-width:600px;background:#fff2f1;color:#a7332c}.empty-execution{margin:90px auto;max-width:550px;border-radius:18px;text-align:center}.empty-execution h1{font-size:20px;color:#1b345f}@media(max-width:600px){.execution-toolbar{padding:0 12px}.execution-toolbar .q-btn:last-child{font-size:0;min-width:40px;padding:0}.execution-toolbar .q-btn:last-child:before{content:'Finalizar';font-size:13px}.timer{padding:7px}.execution-page{padding:28px 16px}.execution-summary{align-items:flex-start;flex-direction:column}.question-card{padding:22px 18px}.question-card h2{font-size:19px}.execution-actions{flex-wrap:wrap}.action-spacer{display:none}.execution-actions .q-btn{flex:1}.execution-actions .q-btn:nth-child(3){order:3;flex-basis:100%}}
</style>
