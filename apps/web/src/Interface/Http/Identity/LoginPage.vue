<template>
  <section class="login-page">
    <div class="login-panel">
      <div class="login-copy">
        <span class="brand-mark">C</span>
        <p class="eyebrow">PLATAFORMA DE ESTUDOS</p>
        <h1>Estude com foco. Evolua todos os dias.</h1>
        <p>Organize seus cadernos, acompanhe seu desempenho e mantenha sua rotina de preparacao.</p>
        <div class="login-benefits"><span>Metas semanais</span><span>Cadernos personalizados</span><span>Revisoes inteligentes</span></div>
      </div>
      <q-card flat class="login-card">
        <q-form @submit.prevent="submit">
        <q-card-section><h2>Bem-vindo de volta</h2><p>Entre para continuar sua preparacao.</p></q-card-section>
        <q-card-section class="q-gutter-md">
          <q-input v-model="email" outlined label="E-mail" type="email" autocomplete="email" />
          <q-input v-model="password" outlined label="Senha" type="password" autocomplete="current-password" @keyup.enter="submit" />
          <q-banner v-if="error" rounded class="login-error">{{ error }}</q-banner>
          <q-btn type="submit" unelevated no-caps class="login-submit" label="Entrar na plataforma" :loading="submitting" />
        </q-card-section>
      </q-form>
      </q-card>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'

defineProps<{ readonly submitting: boolean; readonly error: string }>()
const emit = defineEmits<{ submit: [email: string, password: string] }>()
const email = ref('')
const password = ref('')

function submit(): void {
  emit('submit', email.value, password.value)
}
</script>

<style scoped>
.login-page{min-height:100vh;background:linear-gradient(135deg,#eef4ff,#f7f9fc);display:grid;place-items:center;padding:24px}.login-panel{width:min(980px,100%);display:grid;grid-template-columns:1.1fr .9fr;overflow:hidden;border:1px solid #e6ebf4;border-radius:22px;background:#fff;box-shadow:0 24px 70px rgba(35,72,150,.14)}.login-copy{padding:58px;background:linear-gradient(145deg,#245de4,#467cec);color:#fff}.brand-mark{display:inline-grid;place-items:center;width:28px;height:28px;border-radius:9px;background:#fff;color:#3468e8;font-weight:800}.eyebrow{font-size:11px;letter-spacing:.08em;font-weight:700;color:#d9e6ff;margin:24px 0 8px}.login-copy h1{font-size:36px;line-height:1.16;margin:12px 0 18px}.login-copy p{color:#e4edff;line-height:1.65}.login-benefits{display:grid;gap:11px;margin-top:30px;font-size:14px}.login-card{padding:26px 18px;align-self:center}.login-card h2{margin:0;font-size:24px}.login-card p{color:#758198;font-size:14px}.login-submit{width:100%;background:#3468e8;color:#fff;border-radius:9px;padding:10px}.login-error{background:#fff0f0;color:#b42318}@media(max-width:700px){.login-panel{grid-template-columns:1fr}.login-copy{padding:34px}.login-copy h1{font-size:28px}.login-card{padding:22px 10px}}
</style>
