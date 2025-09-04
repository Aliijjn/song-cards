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
    background: '#121212',
    surface: '#1D1D1D',
    primary: '#2A2A2A',
    secondary: '#777777',
    error: '#DC143C',
    'correct': '#1DB954',
    'incorrect': '#DC143C',
    'text': '#FFFFFF',
  },
}

const vuetify = createVuetify({
  components,
  directives,
  defaults: {
    VCard: {
      elevation: 4,
      class: ["pa-2"],
    },
    VBtn: {
      elevation: 4,
    },
    VDivider: {
      class: ["pb-2", "mt-2"],
      color: 'text',
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