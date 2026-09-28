import { onBeforeUnmount, readonly, ref } from 'vue'
import { BrowserSessionStore } from '../../../Infrastructure/Identity/BrowserSessionStore'
import { QuestionCorrectionRealtimeClient, type QuestionCorrectionRealtimeEvent } from '../../../Infrastructure/Realtime/QuestionCorrectionRealtimeClient'
import { BrowserIgnoredQuestionCorrectionResultStore } from "../../../Infrastructure/Realtime/BrowserIgnoredQuestionCorrectionResultStore"

export function useQuestionCorrectionNotifications() {
  const event = ref<QuestionCorrectionRealtimeEvent | null>(null)
  const client = new QuestionCorrectionRealtimeClient()
  const ignoredResults = new BrowserIgnoredQuestionCorrectionResultStore()
  function connect(): void {
    const token = new BrowserSessionStore().accessToken()
    if (token) client.connect(token, received => { if (!ignoredResults.has(received.requestId)) event.value = received })
  }
  function dismiss(): void { event.value = null }
  function ignore(requestId: string): void { ignoredResults.ignore(requestId); if (event.value?.requestId === requestId) event.value = null }
  function isIgnored(requestId: string): boolean { return ignoredResults.has(requestId) }
  onBeforeUnmount(() => client.disconnect())
  return { connect, dismiss, ignore, isIgnored, event: readonly(event) }
}
