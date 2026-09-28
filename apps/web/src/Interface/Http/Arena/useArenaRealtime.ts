import { onBeforeUnmount } from 'vue'
import { BrowserSessionStore } from '../../../Infrastructure/Identity/BrowserSessionStore'
import { ArenaRealtimeClient } from '../../../Infrastructure/Realtime/ArenaRealtimeClient'
export function useArenaRealtime(refresh:(id:string)=>Promise<unknown>){const client=new ArenaRealtimeClient();function connect():void{const token=new BrowserSessionStore().accessToken();if(token)client.connect(token,event=>void refresh(event.duelId))}onBeforeUnmount(()=>client.disconnect());return{connect}}
