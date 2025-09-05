<script setup lang="ts">
import { useSongStore } from "./store.ts";

import Start from './pages/Start.vue'
import Quiz from './pages/Quiz.vue'
import Lost from './pages/Lost.vue'

//=============================================================================

const store = useSongStore();

</script>

<template>
  <v-app>
    <v-main class="mx-auto bg-surface pa-4" style="width: 1080px;">
      <start v-if="store.gameState === 'start'" />
      <quiz v-else-if="store.gameState === 'playing'" />
      <lost v-else />
    </v-main>
  </v-app>
</template>
