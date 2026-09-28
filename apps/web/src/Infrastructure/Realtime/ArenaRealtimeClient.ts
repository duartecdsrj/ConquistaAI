import { io, type Socket } from 'socket.io-client'
export interface ArenaRealtimeEvent { readonly duelId:string }
export class ArenaRealtimeClient { private socket:Socket|null=null; connect(accessToken:string,onEvent:(event:ArenaRealtimeEvent)=>void):void{this.disconnect();this.socket=io(window.location.origin,{path:'/ws/socket.io',auth:{token:accessToken},transports:['websocket','polling'],reconnection:true});this.socket.on('arena:duel-updated',onEvent)} disconnect():void{this.socket?.disconnect();this.socket=null} }
