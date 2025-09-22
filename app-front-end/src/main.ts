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
    secondary: 'hsl(0, 0%, 30%)',
    error: 'hsl(0, 70%, 60%)',

    correct: 'hsl(130, 70%, 60%)',
    incorrect: 'hsl(0, 70%, 60%)',

    easy: 'hsl(130, 70%, 60%)',
    medium: 'hsl(60, 70%, 60%)',
    hard: 'hsl(0, 70%, 60%)',
    custom: 'hsl(0, 0%, 60%)',

    text: 'hsl(0, 0%, 95%)',
    textMuted: 'hsl(0, 0%, 75%)',
    textDivider: 'hsl(0, 0%, 40%)',
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
      height: 40,
      color: 'primary',
    },
    VDivider: {
      class: ["my-3"],
      color: 'text',
      thickness: 2,
    },
    VCardTitle: {
      class: ["text-h5", "mt-n1", "mx-n2"],
    },
    VCardSubtitle: {
      class: ["text-h6", "mt-n2", "mx-n2"],
    },
    VCardText: {
      class: ["mt-n4", "mx-n4"],
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