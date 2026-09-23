<template>
  <q-page class="page">
    <section class="top"><div><p class="eyebrow">ADMINISTRAÇÃO</p><h1>Catálogo</h1><p>Organize concursos, cargos, editais, assuntos e tags.</p></div><q-btn flat no-caps color="primary" label="Atualizar" :loading="loading" @click="loadExams" /></section>
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
        <q-list v-if="syllabi.length" bordered separator class="list"><q-item v-for="syllabus in syllabi" :key="syllabus.id" clickable :active="selectedSyllabus === syllabus.id" active-class="active" @click="selectSyllabus(syllabus.id)"><q-item-section><q-item-label>{{ syllabus.name }}</q-item-label></q-item-section></q-item></q-list><p v-else class="empty">Nenhum edital para este cargo.</p>
      </template>
      <template v-if="selectedSyllabus">
        <div class="level nested"><div><span>4</span><h2>Assuntos</h2></div><q-input v-model="subjectName" dense outlined label="Novo assunto" @keyup.enter="addSubject"><template #append><q-btn flat dense no-caps color="primary" label="Adicionar" :loading="saving" @click="addSubject" /></template></q-input></div>
        <div v-if="subjects.length" class="chips"><q-chip v-for="subject in subjects" :key="subject.id" color="blue-1" text-color="primary">{{ subject.name }}</q-chip></div><p v-else class="empty">Nenhum assunto para este edital.</p>
      </template>
    </q-card-section></q-card>
    <q-card flat class="tag-card"><q-card-section><div class="level"><div><p class="eyebrow">CLASSIFICAÇÃO</p><h2>Tags</h2></div><q-input v-model="tagName" dense outlined label="Nova tag" @keyup.enter="addTag"><template #append><q-btn flat dense no-caps color="primary" label="Adicionar" :loading="saving" @click="addTag" /></template></q-input></div><div v-if="tags.length" class="chips"><q-chip v-for="tag in tags" :key="tag.id" outline color="primary">{{ tag.name }}</q-chip></div><p v-else class="empty">Nenhuma tag cadastrada.</p></q-card-section></q-card>
  </q-page>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { catalogUseCases } from '../../../Infrastructure/Container'
import { useCatalog } from './useCatalog'
const { chooseExam, choosePosition, chooseSyllabus, error, exams, loadExams, loading, positions, save, saving, subjects, syllabi, tags } = useCatalog()
const selectedExam = ref(''); const selectedPosition = ref(''); const selectedSyllabus = ref('')
const examName = ref(''); const positionName = ref(''); const syllabusName = ref(''); const subjectName = ref(''); const tagName = ref('')
async function selectExam(id: string): Promise<void> { selectedExam.value = id; selectedPosition.value = ''; selectedSyllabus.value = ''; await chooseExam(id) }
async function selectPosition(id: string): Promise<void> { selectedPosition.value = id; selectedSyllabus.value = ''; await choosePosition(id) }
async function selectSyllabus(id: string): Promise<void> { selectedSyllabus.value = id; await chooseSyllabus(id) }
async function addExam(): Promise<void> { await save(async () => { const item = await catalogUseCases.createExam(examName.value, '', null); examName.value = ''; await loadExams(); await selectExam(item.id) }) }
async function addPosition(): Promise<void> { await save(async () => { await catalogUseCases.createPosition(selectedExam.value, positionName.value, ''); positionName.value = ''; await chooseExam(selectedExam.value) }) }
async function addSyllabus(): Promise<void> { await save(async () => { await catalogUseCases.createSyllabus(selectedPosition.value, syllabusName.value); syllabusName.value = ''; await choosePosition(selectedPosition.value) }) }
async function addSubject(): Promise<void> { await save(async () => { await catalogUseCases.createSubject(selectedSyllabus.value, subjectName.value); subjectName.value = ''; await chooseSyllabus(selectedSyllabus.value) }) }
async function addTag(): Promise<void> { await save(async () => { await catalogUseCases.createTag(tagName.value); tagName.value = ''; await loadExams() }) }
onMounted(loadExams)
</script>
<style scoped>
.page{max-width:1080px;margin:auto;padding:42px 34px}.top,.level{display:flex;align-items:center;justify-content:space-between;gap:18px}.top{margin-bottom:25px}.eyebrow{margin:0 0 7px;color:#7187ad;font-size:11px;font-weight:800;letter-spacing:.1em}.top h1{margin:0;color:#142950;font-size:32px}.top p:not(.eyebrow){margin:8px 0 0;color:#71819e}.catalog-card,.tag-card{border:1px solid #e5ecf6;border-radius:17px;background:#fff;box-shadow:0 8px 26px rgba(33,58,105,.04)}.tag-card{margin-top:18px}.level h2{margin:0;color:#1a315b;font-size:18px}.level>div{display:flex;align-items:center;gap:8px}.level span{display:grid;place-items:center;width:25px;height:25px;border-radius:50%;background:#e6efff;color:#2466d4;font-size:12px;font-weight:800}.level .q-field{width:310px}.list{margin-top:13px;border-radius:10px;overflow:hidden}.active{background:#edf4ff;color:#195fce}.nested{margin-top:26px;padding-top:24px;border-top:1px solid #edf1f7}.chips{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}.empty{color:#7a8ba6;font-size:13px;margin:14px 0 0}.error{margin-bottom:14px;background:#fff3f2;color:#ae2f25}@media(max-width:600px){.page{padding:28px 16px}.top,.level{align-items:flex-start;flex-direction:column}.level .q-field{width:100%}}
</style>
