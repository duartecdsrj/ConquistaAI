import { onBeforeUnmount, onMounted, ref } from 'vue'
import { getBlobObjectUrl } from '../../../Infrastructure/Http/AxiosApiClient'

const wait = (milliseconds: number): Promise<void> => new Promise((resolve) => window.setTimeout(resolve, milliseconds))

export function useQuestionAsset(url: string) {
  const source = ref('')
  let disposed = false

  const revoke = (): void => {
    if (source.value) URL.revokeObjectURL(source.value)
    source.value = ''
  }

  onMounted(async () => {
    for (let attempt = 0; attempt < 3 && !disposed; attempt += 1) {
      try {
        const next = await getBlobObjectUrl(url)
        if (disposed) URL.revokeObjectURL(next)
        else source.value = next
        return
      } catch {
        if (attempt < 2) await wait((attempt + 1) * 500)
      }
    }
  })

  onBeforeUnmount(() => {
    disposed = true
    revoke()
  })

  return { source }
}
