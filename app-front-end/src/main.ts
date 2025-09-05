// src/main.ts
import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'
import { createVuetify } from 'vuetify'
import 'vuetify/styles' // Global CSS
import { aliases, mdi } from 'vuetify/iconsets/mdi'

import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const customDarkMode = {
  dark: true,
  colors: {
    background: 'hsl(0, 0%, 0%)',
    surface: 'hsl(0, 0%, 8%)',
    primary: 'hsl(0, 0%, 16%)',
    secondary: '#777777',
    error: '#DC143C',
    'correct': '#1DB954',
    'incorrect': '#DC143C',
    'text': 'hsl(0, 0%, 95%)',
    'textMuted': 'hsl(0, 0%, 70%)',
    'textDivider': 'hsl(0, 0%, 40%)',
  },
}

const vuetify = createVuetify({
  components,
  directives,
  defaults: {
    VCard: {
      elevation: 4,
      color: 'primary',
      class: ["pa-2"],
    },
    VBtn: {
      elevation: 4,
      color: 'primary',
    },
    VDivider: {
      class: ["pb-2", "mt-2"],
      color: 'text',
    },
    VCardTitle: {
      class: ["mt-n1", "mx-n2", "text-text"],
    },
    VCardSubtitle: {
      class: ["mt-n2", "mx-n2", "text-textMuted"],
    },
  },
  theme: {
    defaultTheme: 'customDarkMode',
    themes: {
      customDarkMode,
    },
  },
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: { mdi },
  },
})

createApp(App)
  .use(vuetify)
  .use(createPinia())
  .mount('#app')