<template>
  <div class="content">
    <template v-for="(block,index) in blocks" :key="index">
      <pre v-if="block.kind==='code'" class="code"><code>{{ block.value }}</code></pre>
      <table v-else-if="block.kind==='table'" class="table"><tbody><tr v-for="(row,rowIndex) in block.rows" :key="rowIndex"><component :is="rowIndex===0?'th':'td'" v-for="(cell,cellIndex) in row" :key="cellIndex">{{ cell }}</component></tr></tbody></table>
      <section v-else-if="block.kind==='matching'" class="matching" aria-label="Quadro de correlação">
        <p v-if="block.value" class="matching-intro">{{ block.value }}</p>
        <div class="matching-grid">
          <div class="matching-column"><strong class="matching-title">Itens a correlacionar</strong><ul><li v-for="item in block.left" :key="item.label"><span class="matching-label">{{ item.label }}</span>{{ item.value }}</li></ul></div>
          <div class="matching-column"><strong class="matching-title">Afirmações para relacionar</strong><ol class="matching-assertions"><li v-for="item in block.right" :key="item"><span>( )</span>{{ item }}</li></ol></div>
        </div>
      </section>
      <p v-else>{{ block.value }}</p>
    </template>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
const props = defineProps<{ readonly value: string }>()
type MatchingItem = { readonly label: string; readonly value: string }
type Block = { kind: 'text' | 'code' | 'table'; value: string; rows?: string[][] } | { kind: 'matching'; value: string; left: readonly MatchingItem[]; right: readonly string[] }

function matching(value: string): Block | null {
  const firstAssertion = value.search(/\(\s*\)/u)
  if (firstAssertion < 0) return null
  const before = value.slice(0, firstAssertion)
  const after = value.slice(firstAssertion)
  const starts = [...before.matchAll(/(?:^|\s)(\d{1,2}|[IVXLCDM]+|[A-Z])([.)])\s*/gu)]
  if (starts.length < 2) return null
  const first = starts[0]
  const firstOffset = (first.index ?? 0) + first[0].lastIndexOf(first[1] + first[2])
  const left = starts.map((start, index) => {
    const offset = (start.index ?? 0) + start[0].lastIndexOf(start[1] + start[2])
    const textStart = offset + start[1].length + start[2].length
    const next = starts[index + 1]
    const textEnd = next ? (next.index ?? 0) + next[0].lastIndexOf(next[1] + next[2]) : before.length
    return { label: start[1], value: before.slice(textStart, textEnd).trim() }
  }).filter((item) => item.value.length > 0)
  const right = after.split(/\(\s*\)\s*/u).slice(1).map((item) => item.trim()).filter(Boolean)
  if (left.length < 2 || right.length < 2) return null
  return { kind: 'matching', value: before.slice(0, firstOffset).trim(), left, right }
}

function codeBlock(value: string): string {
  const firstNewline = value.indexOf('\n')
  const firstLine = firstNewline < 0 ? value : value.slice(0, firstNewline)
  // The fence language is presentation metadata, never source code.
  return /^[a-z0-9_+#.-]+$/iu.test(firstLine) ? (firstNewline < 0 ? '' : value.slice(firstNewline + 1)) : value
}

function looksLikeCode(value: string): boolean {
  return /(?:^|\n)\s*(?:SELECT\b|INSERT\b|UPDATE\b|DELETE\b|CREATE\b|function\b|class\b|(?:const|let|var|public|private|static|def|import|from)\b|if\s*\(|for\s*\(|while\s*\(|\$[A-Za-z_]\w*\s*=|#!|(?:curl|grep|chmod|chown|git|docker|npm|composer|php|python3?|node|kubectl)\b(?:\s|$)|<\/?[A-Za-z][^>]*>|[{\[]\s*[A-Za-z_$][\w$]*\s*:)/imu.test(value)
}

const blocks = computed<Block[]>(() => {
  const out: Block[] = []
  const parts = props.value.replace(/\r/g, '').split(String.fromCharCode(96).repeat(3))
  for (let partIndex = 0; partIndex < parts.length; partIndex++) {
    const part = parts[partIndex]
    if (partIndex % 2 === 1) { out.push({ kind: 'code', value: codeBlock(part) }); continue }
    const correlation = matching(part.trim())
    if (correlation !== null) { out.push(correlation); continue }
    for (const item of part.split(/\n{2,}/)) {
      const lines = item.trim().split('\n')
      if (lines.length >= 2 && lines.every((line) => line.includes('|'))) out.push({ kind: 'table', value: '', rows: lines.filter((line) => !/^\s*\|?[-: ]+\|/.test(line)).map((line) => line.split('|').map((cell) => cell.trim()).filter(Boolean)) })
      else if (looksLikeCode(item)) out.push({ kind: 'code', value: item })
      else if (item.trim()) out.push({ kind: 'text', value: item.trim() })
    }
  }
  return out
})
</script>

<style scoped>
.content p{white-space:pre-line;line-height:1.5}.code{overflow:auto;padding:14px;background:#10213f;color:#e7f0ff;border-radius:10px;font:12px/1.5 monospace}.table{width:100%;border-collapse:collapse;border:1px solid #dce6f4}.table :deep(th),.table :deep(td){padding:8px;border:1px solid #dce6f4;text-align:left}.table :deep(th){background:#edf4ff}
.matching{margin:14px 0}.matching-intro{margin:0 0 12px}.matching-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.matching-column{padding:14px;background:#f6f8fc;border:1px solid #dce6f4;border-radius:10px}.matching-title{display:block;margin-bottom:8px;color:#153a78;font-size:13px}.matching-column ol{margin:0;padding-left:28px}.matching-column li{padding:5px 0;line-height:1.45}.matching-assertions{list-style:none;padding-left:0!important}.matching-assertions li{display:flex;gap:8px}.matching-assertions span{font-weight:700;color:#1469e8;white-space:nowrap}@media(max-width:599px){.matching-grid{grid-template-columns:1fr}}
</style>
