import { onBeforeUnmount, readonly, ref } from 'vue'
import { BrowserSessionStore } from '../../../Infrastructure/Identity/BrowserSessionStore'
import { QuestionCorrectionRealtimeClient, type QuestionCorrectionRealtimeEvent } from '../../../Infrastructure/Realtime/QuestionCorrectionRealtimeClient'

export function useQuestionCorrectionNotifications() {
  const event = ref<QuestionCorrectionRealtimeEvent | null>(null)
  const client = new QuestionCorrectionRealtimeClient()
  function connect(): void {
    const token = new BrowserSessionStore().accessToken()
    if (token) client.connect(token, received => { event.value = received })
  }
  function dismiss(): void { event.value = null }
  onBeforeUnmount(() => client.disconnect())
  return { connect, dismiss, event: readonly(event) }
}
