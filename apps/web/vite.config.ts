import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { quasar, transformAssetUrls } from '@quasar/vite-plugin';

export default defineConfig({
  plugins: [
    vue({ template: { transformAssetUrls } }),
    quasar({ sassVariables: new URL('./src/styles/quasar.variables.sass', import.meta.url).pathname }),
  ],
  server: {
    hmr: { host: 'localhost', clientPort: 8081, protocol: 'ws' },
  },
});
