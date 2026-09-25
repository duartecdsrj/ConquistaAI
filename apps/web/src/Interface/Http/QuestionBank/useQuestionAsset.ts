import { onBeforeUnmount, onMounted, ref } from 'vue'
import { getBlobObjectUrl } from '../../../Infrastructure/Http/AxiosApiClient'
export function useQuestionAsset(url:string){const source=ref('');onMounted(async()=>{source.value=await getBlobObjectUrl(url)});onBeforeUnmount(()=>{if(source.value)URL.revokeObjectURL(source.value)});return{source}}
