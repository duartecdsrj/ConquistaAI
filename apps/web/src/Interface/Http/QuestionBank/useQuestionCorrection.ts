import { readonly, ref } from 'vue'
import type { QuestionCorrectionRequest } from '../../../Domain/QuestionBank/QuestionRepository'
import { questionUseCases } from '../../../Infrastructure/Container'
export function useQuestionCorrection(){
 const loading=ref(false),error=ref(''),request=ref<QuestionCorrectionRequest|null>(null)
 async function submit(questionId:string,instruction:string):Promise<boolean>{loading.value=true;error.value='';try{request.value=await questionUseCases.requestCorrection.execute(questionId,instruction);return true}catch(reason){error.value=reason instanceof Error?reason.message:'Não foi possível enviar a correção.';return false}finally{loading.value=false}}
 async function refresh(questionId:string):Promise<void>{try{request.value=await questionUseCases.latestCorrection.execute(questionId)}catch(reason){if(request.value?.status!=='PENDING'&&request.value?.status!=='PROCESSING')error.value=reason instanceof Error?reason.message:'Não foi possível consultar a proposta.'}}
 async function approve():Promise<boolean>{if(!request.value)return false;loading.value=true;error.value='';try{request.value=await questionUseCases.approveCorrection.execute(request.value.id);return true}catch(reason){error.value=reason instanceof Error?reason.message:'Não foi possível aprovar a proposta.';return false}finally{loading.value=false}}
 return{approve,error:readonly(error),loading:readonly(loading),refresh,request:readonly(request),submit}
}
