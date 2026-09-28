import { io, type Socket } from 'socket.io-client'

export interface QuestionCorrectionRealtimeEvent {
  readonly userId: string
  readonly requestId: string
  readonly questionId: string
  readonly status: 'PROPOSED' | 'FAILED'
}

export class QuestionCorrectionRealtimeClient {
  private socket: Socket | null = null

  public connect(accessToken: string, onEvent: (event: QuestionCorrectionRealtimeEvent) => void): void {
    this.disconnect()
    this.socket = io(window.location.origin, {
      path: '/ws/socket.io',
      auth: { token: accessToken },
      transports: ['websocket', 'polling'],
      reconnection: true,
    })
    this.socket.on('question-correction', onEvent)
  }

  public disconnect(): void { this.socket?.disconnect(); this.socket = null }
}
