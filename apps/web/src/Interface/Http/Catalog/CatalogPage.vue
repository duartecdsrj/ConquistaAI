<template>
  <q-page class="page">
    <section class="top">
      <div><p class="eyebrow">ADMINISTRAÇÃO</p><h1 class="page-title">Catálogo</h1><p>Organize concursos, cargos, editais, assuntos e tags.</p></div>
      <q-btn flat no-caps color="primary" label="Atualizar" :loading="loading" @click="loadExams" />
    </section>
    <q-banner v-if="error" rounded class="error">{{ error }}</q-banner>
    <q-inner-loading :showing="loading" color="primary" />

    <q-card flat class="catalog-card"><q-card-section>
      <div class="level"><div><span>1</span><h2>Concursos</h2></div><q-input v-model="examName" dense outlined label="Novo concurso" @keyup.enter="addExam"><template #append><q-btn flat dense no-caps color="primary" label="Adicionar" :loading="saving" @click="addExam" /></template></q-input></div>
      <q-list v-if="exams.length" bordered separator class="list"><q-item v-for="exam in exams" :key="exam.id" clickable :active="selectedExam === exam.id" active-class="active" @click="selectExam(exam.id)"><q-item-section><q-item-label>{{ exam.name }}</q-item-label><q-item-label caption>{{ exam.organizer || 'Organizadora não informada' }} {{ exam.year ? '· ' + exam.year : '' }}</q-item-label></q-item-section></q-item></q-list><p v-else class="empty">Nenhum concurso cadastrado.</p>

      <template v-if="selectedExam">
        <div class="level nested"><div><span>2</span><h2>Cargos</h2></div><q-input v-model="positionName" dense outlined label="Novo cargo" @keyup.enter="addPosition"><template #append><q-btn flat dense no-caps color="primary" label="Adicionar" :loading="saving" @click="addPosition" /></template></q-input></div>
        <q-list v-if="positions.length" bordered separator class="list"><q-item v-for="position in positions" :key="position.id" clickable :active="selectedPosition === position.id" active-class="active" @click="selectPosition(position.id)"><q-item-section><q-item-label>{{ position.name }}</q-item-label><q-item-label caption>{{ position.emphasis || 'Sem ênfase' }}</q-item-label></q-item-section></q-item></q-list><p v-else class="empty">Nenhum cargo para este concurso.</p>
      </template>

      <template v-if="selectedPosition">
        <div class="level nested"><div><span>3</span><h2>Editais</h2></div><q-input v-model="syllabusName" dense outlined label="Novo edital" @keyup.enter="addSyllabus"><template #append><q-btn flat dense no-caps color="primary" label="Adicionar" :loading="saving" @click="addSyllabus" /></template></q-input></div>
        <q-list v-if="syllabi.length" bordered separator class="list"><q-item v-for="syllabus in syllabi" :key="syllabus.id" clickable :active="selectedSyllabus === syllabus.id" active-class="active" @click="selectSyllabus(syllabus.id)"><q-item-section><q-item-label>{{ syllabus.name }}</q-item-label><q-item-label caption>{{ syllabus.documentOriginalName ? 'PDF: ' + syllabus.documentOriginalName : 'Sem PDF enviado' }}</q-item-label></q-item-section><q-item-section side v-if="syllabus.documentSha256"><q-icon name="picture_as_pdf" color="negative" size="20px" /></q-item-section></q-item></q-list><p v-else class="empty">Nenhum edital para este cargo.</p>
      </template>

      <template v-if="selectedSyllabus">
        <div class="document-panel nested">
          <div><p class="eyebrow">DOCUMENTO OFICIAL</p><h2>PDF do edital</h2><p>O arquivo é preservado com hash SHA-256 para evitar gravações duplicadas.</p></div>
          <div class="upload-controls"><q-file v-model="documentFile" dense outlined accept="application/pdf,.pdf" label="Selecionar PDF" clearable><template #prepend><q-icon name="picture_as_pdf" /></template></q-file><q-btn no-caps unelevated color="primary" label="Enviar PDF" :disable="!documentFile" :loading="saving" @click="uploadDocument" /></div>
        </div>
        <div class="processing-panel nested"><div><p class="eyebrow">PROCESSAMENTO</p><h2>Texto extraído do edital</h2><p v-if="!processingJob">Envie o PDF e inicie a extração para revisar as páginas e sua proveniência.</p><p v-else>Estado: <strong>{{ processingJob.status }}</strong> · {{ processingJob.progress }}%</p><q-linear-progress v-if="processingJob" rounded size="8px" color="primary" :value="processingJob.progress / 100" /><q-banner v-if="processingJob?.errorMessage" dense rounded class="error">{{ processingJob.errorMessage }}</q-banner></div><div class="processing-actions"><q-btn no-caps unelevated color="primary" :disable="!syllabi.find((item) => item.id === selectedSyllabus)?.documentSha256" :loading="saving" label="Processar PDF" @click="processDocument(false)" /><q-btn v-if="processingJob" no-caps outline color="primary" :loading="saving" label="Atualizar status" @click="refreshProcessing" /><q-btn v-if="processingJob?.status === completedStatus || processingJob?.status === failedStatus" no-caps flat color="primary" :loading="saving" label="Reprocessar" @click="processDocument(true)" /></div></div><div v-if="processingJob?.status === completedStatus" class="extraction-panel"><p class="eyebrow">REVISÃO ADMINISTRATIVA</p><h3>Páginas extraídas</h3><q-expansion-item v-for="page in extractions" :key="page.pageNumber" dense dense-toggle expand-separator :label="pageLabel(page)" :caption="pageCaption(page)"><q-card flat bordered><q-card-section class="extracted-text">{{ page.textContent || emptyExtractionText }}</q-card-section></q-card></q-expansion-item><p v-if="!extractions.length" class="empty">O processamento terminou, mas não encontrou páginas para revisão.</p></div>
        <div class="level nested"><div><span>4</span><h2>Assuntos</h2></div><q-input v-model="subjectName" dense outlined label="Novo assunto" @keyup.enter="addSubject"><template #append><q-btn flat dense no-caps color="primary" label="Adicionar" :loading="saving" @click="addSubject" /></template></q-input></div>
        <div class="provenance"><q-input v-model="sourcePage" dense outlined type="number" min="1" label="Página de origem (opcional)" /><q-input v-model="sourceExcerpt" dense outlined type="textarea" autogrow label="Trecho do edital (opcional)" /></div>
        <div v-if="subjects.length" class="chips"><q-chip v-for="subject in subjects" :key="subject.id" clickable :outline="activeSubjectId !== subject.id" color="blue-1" text-color="primary" @click="activeSubjectId = subject.id">{{ subject.name }}<q-tooltip v-if="subject.sourceExcerpt">Página {{ subject.sourcePage || 'não informada' }} · {{ subject.sourceExcerpt }}</q-tooltip></q-chip></div><p v-else class="empty">Nenhum assunto para este edital.</p>
        <div v-if="activeSubjectId" class="taxonomy-link"><div><p class="eyebrow">TAXONOMIA CANÔNICA</p><h3>Associar assunto selecionado</h3></div><q-select v-model="selectedTaxonomyIds" dense outlined multiple emit-value map-options option-value="value" option-label="label" :options="taxonomyOptions" label="Assuntos canônicos" /><q-btn unelevated no-caps color="primary" label="Salvar associação" :loading="saving" @click="assignTaxonomy" /></div>
      </template>
    </q-card-section></q-card>

    <q-card flat class="tag-card"><q-card-section><div class="level"><div><p class="eyebrow">CLASSIFICAÇÃO</p><h2>Tags</h2></div><q-input v-model="tagName" dense outlined label="Nova tag" @keyup.enter="addTag"><template #append><q-btn flat dense no-caps color="primary" label="Adicionar" :loading="saving" @click="addTag" /></template></q-input></div><div v-if="tags.length" class="chips"><q-chip v-for="tag in tags" :key="tag.id" outline color="primary">{{ tag.name }}</q-chip></div><p v-else class="empty">Nenhuma tag cadastrada.</p></q-card-section></q-card>
  </q-page>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useCatalog } from './useCatalog'

const { assignTaxonomySubjects, chooseExam, choosePosition, chooseSyllabus, createExam, createPosition, createSubject, createSyllabus, createTag, error, exams, extractions, loadExams, loading, loadTaxonomyOptions, positions, processingJob, queueSyllabusProcessing, refreshSyllabusProcessing, saving, subjects, syllabi, tags, taxonomyOptions, uploadSyllabusDocument } = useCatalog()
const completedStatus = "COMPLETED"; const failedStatus = "FAILED"; const emptyExtractionText = "Página sem texto extraível."
const selectedExam = ref(''); const selectedPosition = ref(''); const selectedSyllabus = ref(''); const activeSubjectId = ref(''); const selectedTaxonomyIds = ref<string[]>([])
const examName = ref(''); const positionName = ref(''); const syllabusName = ref(''); const subjectName = ref(''); const sourceExcerpt = ref(''); const sourcePage = ref<number | null>(null); const tagName = ref(''); const documentFile = ref<File | null>(null)
async function selectExam(id: string): Promise<void> { selectedExam.value = id; selectedPosition.value = ''; selectedSyllabus.value = ''; await chooseExam(id) }
async function selectPosition(id: string): Promise<void> { selectedPosition.value = id; selectedSyllabus.value = ''; documentFile.value = null; await choosePosition(id) }
async function selectSyllabus(id: string): Promise<void> { selectedSyllabus.value = id; activeSubjectId.value = ''; selectedTaxonomyIds.value = []; documentFile.value = null; await chooseSyllabus(id) }
async function addExam(): Promise<void> { const item = await createExam(examName.value); if (item) { examName.value = ''; await selectExam(item.id) } }
async function addPosition(): Promise<void> { if (await createPosition(selectedExam.value, positionName.value)) positionName.value = '' }
async function addSyllabus(): Promise<void> { if (await createSyllabus(selectedPosition.value, syllabusName.value)) syllabusName.value = '' }
async function uploadDocument(): Promise<void> { if (!documentFile.value) return; if (await uploadSyllabusDocument(selectedSyllabus.value, selectedPosition.value, documentFile.value)) documentFile.value = null }
 function pageLabel(page: { pageNumber: number }): string { return 'Página ' + page.pageNumber }
function pageCaption(page: { startOffset: number; endOffset: number }): string { return 'Offsets ' + page.startOffset + '–' + page.endOffset }
async function processDocument(reprocess: boolean): Promise<void> { await queueSyllabusProcessing(selectedSyllabus.value, reprocess) }
async function refreshProcessing(): Promise<void> { await refreshSyllabusProcessing(selectedSyllabus.value) }
async function addSubject(): Promise<void> { if (await createSubject(selectedSyllabus.value, subjectName.value, { sourceExcerpt: sourceExcerpt.value.trim() || undefined, sourcePage: sourcePage.value || undefined })) { subjectName.value = ''; sourceExcerpt.value = ''; sourcePage.value = null } }
async function assignTaxonomy(): Promise<void> { await assignTaxonomySubjects(activeSubjectId.value, selectedTaxonomyIds.value) }
async function addTag(): Promise<void> { if (await createTag(tagName.value)) tagName.value = '' }
onMounted(async () => { await Promise.all([loadExams(), loadTaxonomyOptions()]) })
</script>

<style scoped>
.page{max-width:1080px;margin:auto;padding:42px 34px}.top,.level{display:flex;align-items:center;justify-content:space-between;gap:18px}.top{margin-bottom:25px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1{margin:0;color:#142950;font-size:32px}.top p:not(.eyebrow),.document-panel p:not(.eyebrow){margin:8px 0 0;color:#71819e}.catalog-card,.tag-card{border:1px solid #e5ecf6;border-radius:17px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.04)}.tag-card{margin-top:18px}.level h2,.document-panel h2{margin:0;color:#1a315b;font-size:18px}.level>div{display:flex;align-items:center;gap:8px}.level span{display:grid;place-items:center;width:25px;height:25px;border-radius:50%;background:#e6efff;color:#2466d4;font-size:12px;font-weight:800}.level .q-field{width:310px}.list{margin-top:13px;border-radius:10px;overflow:hidden}.active{background:#edf4ff;color:#195fce}.nested{margin-top:26px;padding-top:24px;border-top:1px solid #edf1f7}.processing-panel{display:flex;gap:28px;align-items:center;justify-content:space-between}.processing-panel h2,.extraction-panel h3{margin:0;color:#1a315b;font-size:18px}.processing-panel p:not(.eyebrow){margin:8px 0;color:#71819e}.processing-panel .q-linear-progress{margin-top:10px}.processing-actions{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}.extraction-panel{margin-top:14px;padding:16px;border:1px solid #e5ecf6;border-radius:12px;background:#f8fbff}.extracted-text{white-space:pre-wrap;color:#334b73;max-height:340px;overflow:auto}.document-panel{display:flex;gap:28px;align-items:center;justify-content:space-between}.upload-controls{display:flex;align-items:center;gap:10px;min-width:440px}.upload-controls .q-field{flex:1}.taxonomy-link{display:grid;grid-template-columns:1fr 1.4fr auto;gap:12px;align-items:center;margin-top:18px;padding:16px;border:1px solid #e5ecf6;border-radius:12px;background:#f8fbff}.taxonomy-link h3{margin:0;color:#1a315b;font-size:15px}.provenance{display:grid;grid-template-columns:180px 1fr;gap:10px;margin-top:12px}.chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}.empty{color:#7a8ba6;font-size:13px;margin:14px 0 0}.error{margin-bottom:14px;background:#fff3f2;color:#ae2f25}@media(max-width:600px){.taxonomy-link,.provenance{grid-template-columns:1fr}.provenance{grid-template-columns:1fr}.page{padding:28px 16px}.top,.level,.document-panel,.processing-panel,.upload-controls{align-items:stretch;flex-direction:column}.level .q-field,.upload-controls{width:100%;min-width:0}}
</style>
