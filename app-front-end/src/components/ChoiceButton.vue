<script setup lang="ts">

import { ref, toRef, computed, watch, onMounted } from 'vue';
import {
  type SongData,
  type GuessableStatKey,
  guessableStatKeys,
  type ButtonData,
  type GameData,
  type GameState,
  type SessionData,
} from '../types/types';

const props = defineProps<{
  button: ButtonData | null
}>()
const button = toRef(props, 'button')

const loading = ref(true);
const image = ref(null);

const emit = defineEmits(['click'])

function getColour() {
  switch (button.value?.state) {
    case 'correct':   return 'correct'
    case 'incorrect': return 'incorrect'
    default:          return 'primary'
  }
}

const parseSongStat = computed((): string[] => {
  const val = button.value?.song[button.value.songStat] as number
  switch (button.value?.songStat) {
    case 'song_duration':          return ["Duration: ",     `${Math.floor(val / 60)}:${(val % 60).toString().padStart(2, "0")}`]
    case 'release_date':           return ["Release Year: ", `${val}`]
    case 'total_views_on_spotify': return ["Streams: ",      `${val}m`]
    default:                       return [":(",             "this shouldn't happen"]
  }
})

const albumCoverUrl = computed((): string => {
  return `http://localhost:8080/api/album-cover/${
    button.value?.song.album
      .replace(/[,&@!#%$^*()_+=]/g, '')
      .trim()
      .replace(/\s+/g, '-')
      .toLowerCase()
  }`
})

</script>

<template>
  <v-col cols="12" sm="6">
    <v-card
      class="mb-0 card-transition"
      color="primary"
      elevation="4"
      @click="emit('click', button?.index ?? 0)"
      :loading="button === null"
      :disabled="button?.state !== 'default'"
    >
      <v-card-title v-text="button?.song.title" />
      <v-card-subtitle class="mt-n2" v-text="button?.song.artist" />

      <v-img
        :src="albumCoverUrl"
        aspect-ratio="1"
        class="ma-3"
        @loadstart="loading = true"
        @load="loading = false"
      />

      <v-card-title
        v-if="button?.state && button?.state !== 'default'"
        :class="`text-${button?.state} song-stat-transition`"
      >
        <span>{{ parseSongStat[0] }}</span>
        <span class="font-weight-bold">{{ parseSongStat[1] }}</span>
      </v-card-title>
    </v-card>
  </v-col>
</template>

<style scoped>
.card-transition {
  transition: all 1s ease;
}

.song-stat-transition {
  transition: all 1s ease;
  overflow: hidden;
}
</style>