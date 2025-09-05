<script setup lang="ts">
import { onMounted } from 'vue'
import { useSongStore } from "../store.ts";
import { getAlbumUrl } from "../tools.ts";
import TextDivider from "../components/TextDivider.vue";

//=============================================================================

const store = useSongStore();

onMounted(async () => {
  if (!store.genres.length) {
    await store.fetchGenres();
  }
})

function startMatch(genreId: number | null = null) {
  store.selectedGenreId = genreId;
  store.gameState = 'playing';
}
</script>

<template>
  <v-card-title class="text-h5" v-text="'Welcome to Spotify Higher-Lower!'" />

  <v-row no-gutters>
    <v-col cols="12">
      <v-card class="ma-2" @click="startMatch">
        <v-card-title class="text-h6 text-left" v-text="'Quick Match'" />
        <v-card-subtitle v-text="'Start a match with songs from all genres'" />
      </v-card>
    </v-col>
  </v-row>

  <text-divider text="Decades" />

  <v-card-subtitle v-if="!store.decades.length" v-text="'no decades found'" />
  <v-row v-else no-gutters>
    <v-col v-for="decade in store.decades" :key="decade.id" cols="12" sm="6" md="4">
      <v-card class="ma-2" @click="startMatch(decade.id)">
        <v-card-title class="text-h6" v-text="decade.name" />
        <v-card-subtitle v-text="decade.description" />
        <v-img :src="getAlbumUrl(decade.showcased_album)" class="ma-1" aspect-ratio="1" />
      </v-card>
    </v-col>
  </v-row>

  <text-divider text="Genres" />

  <v-card-subtitle v-if="!store.genres.length" v-text="'no genres found'" />
  <v-row v-else no-gutters>
    <v-col v-for="genre in store.genres" :key="genre.id" cols="12" sm="6" md="4">
      <v-card class="ma-2" @click="startMatch(genre.id)">
        <v-card-title class="text-h6" v-text="genre.name" />
        <v-card-subtitle v-text="genre.description" />
        <v-img :src="getAlbumUrl(genre.showcased_album)" class="ma-1" aspect-ratio="1" />
      </v-card>
    </v-col>
  </v-row>

</template>