export interface AssistantSyllabus { readonly id:string; readonly name:string }
export interface AssistantConversation { readonly id:string; readonly syllabusId:string; readonly title:string; readonly createdAt:string; readonly updatedAt:string }
export interface AssistantEvidence { readonly pageNumber:number; readonly excerpt:string }
export interface AssistantMessage { readonly id:string; readonly conversationId:string; readonly role:'USER'|'ASSISTANT'; readonly content:string; readonly provider:string|null; readonly model:string|null; readonly evidence:readonly AssistantEvidence[]; readonly createdAt:string }
export interface AssistantRepository { syllabi():Promise<readonly AssistantSyllabus[]>; conversations():Promise<readonly AssistantConversation[]>; createConversation(syllabusId:string,title?:string):Promise<AssistantConversation>; messages(conversationId:string):Promise<readonly AssistantMessage[]>; send(conversationId:string,content:string):Promise<AssistantMessage> }
