import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { quasar, transformAssetUrls } from '@quasar/vite-plugin';

export default defineConfig({
  plugins: [
    vue({ template: { transformAssetUrls } }),
    quasar({ sassVariables: new URL('./src/styles/quasar.variables.sass', import.meta.url).pathname }),
  ],
  server: {
    allowedHosts: ['conquistaai.app.br', 'www.conquistaai.app.br', 'nginx'],
  },
});
