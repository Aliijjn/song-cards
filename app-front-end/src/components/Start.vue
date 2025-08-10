<script setup lang="ts">
import type { SessionData } from './../types/types'

const session = defineModel<SessionData>('session');

function startMatch() {
  session!.value!.prevScore = 0;
  session!.value!.state = 'playing'
}
</script>

<template>
  <v-card-title class="text-h5" v-text="'Welcome to Spotify Higher-Lower!'" />

  <v-btn
    class="pa-2 ml-4 mb-2"
    variant="tonal"
    elevation="4"
    @click="startMatch"
    color="correct"
    text="Start Game"
    size="large"
  />

  <v-card-subtitle v-text="`High Score: ${Math.max(session?.highScore ?? 0, 0)}`" />
</template>