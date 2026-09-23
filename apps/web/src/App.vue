<template>
  <q-layout view="hHh Lpr fFf" class="app-shell">
    <q-header class="topbar">
      <q-toolbar class="toolbar">
        <q-btn flat round dense icon="menu" aria-label="Abrir menu" @click="drawer = !drawer" />
        <q-toolbar-title class="brand"><span class="brand-mark">C</span> Concursos</q-toolbar-title>
        <q-btn flat round dense icon="notifications_none" aria-label="NotificaÁıes" />
        <q-avatar size="34px" class="avatar">CS</q-avatar>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="drawer" :width="252" bordered class="drawer">
      <div class="drawer-brand"><span class="brand-mark">C</span><strong>Concursos</strong></div>
      <q-list padding class="nav-list">
        <q-item v-for="item in menu" :key="item.label" clickable :active="active === item.label" active-class="active-nav" @click="active = item.label">
          <q-item-section avatar><q-icon :name="item.icon" /></q-item-section>
          <q-item-section>{{ item.label }}</q-item-section>
        </q-item>
      </q-list>
      <div class="drawer-bottom">
        <q-item clickable><q-item-section avatar><q-icon name="settings" /></q-item-section><q-item-section>ConfiguraÁıes</q-item-section></q-item>
        <q-item clickable><q-item-section avatar><q-icon name="help_outline" /></q-item-section><q-item-section>Ajuda</q-item-section></q-item>
      </div>
    </q-drawer>

    <q-page-container>
      <q-page class="page-content">
        <section class="welcome">
          <div>
            <p class="eyebrow">TER«A-FEIRA, 23 DE SETEMBRO</p>
            <h1>Ol·, Cristiano <span>È›y¯ßy€ßuÁ‚ùÁK</span></h1>
            <p class="muted">Vamos manter o ritmo. VocÍ est· a poucos passos da sua meta semanal.</p>
          </div>
          <q-btn unelevated no-caps class="primary-action" icon="play_arrow" label="Continuar estudando" />
        </section>

        <section class="stats-grid">
          <article v-for="stat in stats" :key="stat.label" class="stat-card">
            <div class="stat-icon" :class="stat.tone"><q-icon :name="stat.icon" /></div>
            <div><p>{{ stat.label }}</p><strong>{{ stat.value }}</strong><small :class="stat.positive ? 'positive' : ''">{{ stat.detail }}</small></div>
          </article>
        </section>

        <section class="content-grid">
          <article class="panel progress-panel">
            <div class="panel-head"><div><h2>Sua meta semanal</h2><p>Continue assim, vocÍ est· indo muito bem.</p></div><q-btn flat dense no-caps label="Ver detalhes" class="link-btn" /></div>
            <div class="progress-main"><q-circular-progress show-value :value="72" size="136px" :thickness="0.15" color="primary" track-color="blue-1" class="progress-ring"><strong>72%</strong><span>da meta</span></q-circular-progress><div><h3>36 de 50 questıes</h3><p class="muted">Faltam 14 questıes para concluir sua meta desta semana.</p><q-linear-progress rounded size="8px" :value="0.72" color="primary" track-color="blue-1" /></div></div>
          </article>

          <article class="panel streak-panel"><div class="flame">&#128293;</div><p class="eyebrow">SEQU√äNCIA ATUAL</p><h2>7 dias</h2><p class="muted">Seu melhor: <strong>12 dias</strong></p><div class="week"><span v-for="day in ['S','T','Q','Q','S','S','D']" :key="day" class="done">{{ day }}</span></div></article>
        </section>

        <section class="section-head"><div><h2>Continue de onde parou</h2><p class="muted">Seus ˙ltimos cadernos de estudo</p></div><q-btn flat no-caps label="Ver todos" class="link-btn" /></section>
        <section class="notebooks">
          <article v-for="book in notebooks" :key="book.title" class="notebook-card">
            <div class="book-top"><div class="book-icon" :class="book.tone"><q-icon :name="book.icon" /></div><q-btn flat round dense icon="more_horiz" /></div>
            <span class="tag">{{ book.tag }}</span><h3>{{ book.title }}</h3><p>{{ book.count }} questıes ∑ {{ book.progress }}% concluÌdo</p>
            <q-linear-progress rounded size="7px" :value="book.progress / 100" :color="book.color" track-color="grey-3" />
            <q-btn unelevated no-caps :color="book.color" class="resume" label="Continuar" />
          </article>
        </section>

        <section class="section-head"><div><h2>Desempenho por assunto</h2><p class="muted">Veja onde concentrar seus prÛximos estudos</p></div><q-btn flat no-caps label="Ver relatÛrio" class="link-btn" /></section>
        <article class="panel subjects"><div v-for="subject in subjects" :key="subject.name" class="subject-row"><div><strong>{{ subject.name }}</strong><span>{{ subject.answers }} questıes respondidas</span></div><div class="subject-score"><q-linear-progress rounded size="8px" :value="subject.score / 100" :color="subject.color" track-color="grey-3" /><b>{{ subject.score }}%</b></div></div></article>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
const drawer = ref(true); const active = ref('InÌcio')
const menu = [{ label:'InÌcio', icon:'home' },{ label:'Estudar', icon:'menu_book' },{ label:'Cadernos', icon:'folder_copy' },{ label:'Desempenho', icon:'insights' },{ label:'Revisıes', icon:'event_repeat' }]
const stats = [{label:'Questıes respondidas',value:'128',detail:'+18 esta semana',icon:'task_alt',tone:'blue',positive:true},{label:'Taxa de acerto',value:'76%',detail:'+4% este mÍs',icon:'trending_up',tone:'green',positive:true},{label:'Tempo de estudo',value:'8h 24m',detail:'esta semana',icon:'schedule',tone:'orange'},{label:'Revisıes pendentes',value:'12',detail:'para hoje',icon:'bookmark_added',tone:'purple'}]
const notebooks = [{tag:'EM ANDAMENTO',title:'Redes de Computadores',count:30,progress:62,icon:'router',tone:'soft-blue',color:'primary'},{tag:'EM ANDAMENTO',title:'SeguranÁa da InformaÁ„o',count:25,progress:40,icon:'shield',tone:'soft-purple',color:'deep-purple'},{tag:'NOVO',title:'Banco de Dados',count:20,progress:0,icon:'storage',tone:'soft-green',color:'teal'}]
const subjects = [{name:'Redes de Computadores',answers:42,score:82,color:'positive'},{name:'Banco de Dados',answers:31,score:71,color:'primary'},{name:'SeguranÁa da InformaÁ„o',answers:28,score:64,color:'warning'}]
</script>

<style scoped>
.app-shell{background:#f7f9fc;color:#1f2a44}.topbar{background:#fff;color:#1f2a44;border-bottom:1px solid #e9edf5}.toolbar{height:66px;padding:0 22px}.brand{font-size:20px;font-weight:750}.brand-mark{display:inline-grid;place-items:center;width:28px;height:28px;margin-right:9px;border-radius:9px;background:#3468e8;color:#fff;font-weight:800}.avatar{margin-left:14px;background:#e7efff;color:#2d5fd6;font-size:11px;font-weight:700}.drawer{background:#fff}.drawer-brand{height:66px;display:flex;align-items:center;padding:0 24px;font-size:19px}.nav-list{margin-top:12px;color:#61708b}.nav-list .q-item{border-radius:10px;margin:5px 12px;font-weight:600}.active-nav{background:#eaf0ff;color:#2f63df}.drawer-bottom{position:absolute;bottom:18px;width:100%;color:#61708b}.page-content{max-width:1320px;margin:auto;padding:42px 38px 60px}.welcome{display:flex;justify-content:space-between;gap:20px;align-items:center;margin-bottom:30px}.eyebrow{font-size:11px;letter-spacing:.08em;font-weight:700;color:#8390a7;margin:0 0 8px}.welcome h1{font-size:30px;margin:0 0 9px}.muted{color:#758198;margin:0;font-size:14px}.primary-action{background:#3468e8;color:#fff;border-radius:10px;padding:9px 15px;font-weight:650}.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:23px}.stat-card,.panel,.notebook-card{background:#fff;border:1px solid #edf0f5;border-radius:15px;box-shadow:0 4px 16px rgba(30,49,92,.035)}.stat-card{padding:19px;display:flex;gap:13px;align-items:center}.stat-icon{width:43px;height:43px;border-radius:12px;display:grid;place-items:center;font-size:21px}.blue{background:#eaf0ff;color:#3568e9}.green{background:#e7f8ef;color:#25a466}.orange{background:#fff1df;color:#f29639}.purple{background:#f1eaff;color:#8559d8}.stat-card p{font-size:12px;color:#7d8aa0;margin:0}.stat-card strong{display:block;font-size:23px;margin:2px 0}.stat-card small{font-size:11px;color:#8692a6}.positive{color:#21a266!important}.content-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:34px}.panel{padding:24px}.panel-head,.section-head{display:flex;justify-content:space-between;align-items:flex-start}.panel h2,.section-head h2{font-size:18px;margin:0 0 5px}.panel-head p{margin:0;color:#758198;font-size:13px}.link-btn{color:#3468e8;font-weight:650}.progress-main{display:flex;gap:31px;align-items:center;margin-top:25px}.progress-ring{color:#27487f;text-align:center}.progress-ring strong{display:block;font-size:25px}.progress-ring span{font-size:11px;color:#7c889c}.progress-main h3{margin:0 0 8px;font-size:17px}.progress-main .muted{max-width:330px;margin-bottom:16px}.streak-panel{text-align:center;background:linear-gradient(145deg,#fff,#fff7ec)}.flame{font-size:35px;margin-bottom:9px}.streak-panel h2{font-size:30px;margin:0}.week{display:flex;justify-content:space-between;margin-top:20px}.week span{display:grid;place-items:center;width:27px;height:27px;border-radius:50%;background:#ffe6be;color:#e68b1f;font-size:11px;font-weight:700}.section-head{margin:20px 0 15px}.notebooks{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.notebook-card{padding:19px}.book-top{display:flex;justify-content:space-between}.book-icon{width:42px;height:42px;border-radius:11px;display:grid;place-items:center;font-size:20px}.soft-blue{background:#e9f1ff;color:#3971ed}.soft-purple{background:#f0eaff;color:#7b52ce}.soft-green{background:#e6f8f0;color:#1f9c68}.tag{display:block;margin-top:17px;color:#8b97a8;font-size:10px;font-weight:700}.notebook-card h3{margin:5px 0;font-size:16px}.notebook-card p{font-size:12px;color:#77849a;margin:0 0 13px}.resume{width:100%;margin-top:17px;border-radius:9px}.subjects{padding:7px 24px}.subject-row{display:flex;justify-content:space-between;align-items:center;padding:15px 0;border-bottom:1px solid #eef1f5}.subject-row:last-child{border:0}.subject-row strong{display:block;font-size:14px}.subject-row span{font-size:12px;color:#7d899d}.subject-score{display:flex;align-items:center;gap:12px;width:40%}.subject-score .q-linear-progress{flex:1}.subject-score b{font-size:13px;width:32px;text-align:right}@media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}.content-grid{grid-template-columns:1fr}.notebooks{grid-template-columns:1fr 1fr}.page-content{padding:28px 20px}.welcome{align-items:flex-start;flex-direction:column}.primary-action{width:100%}.drawer-brand{display:none}}@media(max-width:560px){.stats-grid,.notebooks{grid-template-columns:1fr}.page-content{padding:24px 16px}.welcome h1{font-size:25px}.progress-main{gap:18px}.progress-main .q-circular-progress{min-width:105px!important;width:105px!important;height:105px!important}.subject-row{align-items:flex-start;gap:12px;flex-direction:column}.subject-score{width:100%}.toolbar{padding:0 10px}}
</style>