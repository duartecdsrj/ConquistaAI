import { createServer } from 'node:http'
import { createHmac, timingSafeEqual } from 'node:crypto'
import { Server } from 'socket.io'

const b64 = value => Buffer.from(value, 'base64url')
function user(token) {
  const [header, payload, signature] = String(token || '').split('.')
  if (!header || !payload || !signature) throw Error('token')
  const expected = createHmac('sha256', process.env.JWT_SECRET || '').update(header + '.' + payload).digest('base64url')
  if (expected.length !== signature.length || !timingSafeEqual(Buffer.from(expected), Buffer.from(signature))) throw Error('token')
  const claims = JSON.parse(b64(payload))
  if (!claims.sub || claims.exp * 1000 < Date.now()) throw Error('token')
  return claims.sub
}

const internalSecret = process.env.REALTIME_INTERNAL_SECRET || process.env.JWT_SECRET || ''
const http = createServer((request, response) => {
  if (request.url === '/health') { response.writeHead(200); response.end('ok'); return }
  if (request.method !== 'POST' || request.url !== '/internal/events' || !internalSecret || request.headers['x-realtime-secret'] !== internalSecret) { response.writeHead(404); response.end(); return }
  let body = ''
  request.on('data', chunk => { body += chunk; if (body.length > 32768) request.destroy() })
  request.on('end', () => {
    try {
      const event = JSON.parse(body)
      if (typeof event.userId !== 'string' || !event.userId || typeof event.status !== 'string') throw Error('invalid')
      if (typeof event.duelId === "string" && event.duelId) io.to("user:" + event.userId).emit("arena:duel-updated", { duelId: event.duelId }); else io.to("user:" + event.userId).emit("question-correction", event)
      response.writeHead(202, { 'content-type': 'application/json' }); response.end('{"accepted":true}')
    } catch { response.writeHead(400); response.end() }
  })
})
const io = new Server(http, { path: '/ws/socket.io', cors: { origin: false } })
io.use((socket, next) => { try { socket.data.userId = user(socket.handshake.auth?.token); next() } catch { next(Error('UNAUTHENTICATED')) } })
io.on('connection', socket => socket.join('user:' + socket.data.userId))
http.listen(Number(process.env.REALTIME_PORT || 9010), '0.0.0.0')
