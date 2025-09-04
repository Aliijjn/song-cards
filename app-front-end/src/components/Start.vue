<script setup lang="ts">
import { onMounted } from 'vue'
import type { SessionData } from './../types/types'
import { useSongStore } from "../store.ts";

const session = defineModel<SessionData>('session');

const songStore = useSongStore();

onMounted(async () => {
  if (!songStore.genres.length) {
    await songStore.fetchGenres();
  }
})

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

  <v-divider />
  <v-card-subtitle v-if="!songStore.genres.length" v-text="'no genres found'" />
  <v-row v-else no-gutters>
    <v-col v-for="genre in songStore.genres" :key="genre.id" cols="12" sm="6" md="4">
      <v-card class="ma-2 pa-2" variant="tonal" elevation="4">
        <v-card-title class="text-h6" v-text="genre.name" />
        <v-card-subtitle v-text="genre.description" />
      </v-card>
    </v-col>
  </v-row>
</template>