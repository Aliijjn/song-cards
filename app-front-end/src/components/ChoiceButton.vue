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
import dayjs from 'dayjs';

const props = defineProps<{
  button: ButtonData | null
}>()
const button = toRef(props, 'button')

const loading = ref(true);

const emit = defineEmits(['click'])

function formatStreams(value: number): string {
  const len = value.toString().length - 1;
  const postfixes: { len: number, char: string }[] = [
    { len: 9, char: 'B' },
    { len: 6, char: 'M' },
    { len: 3, char: 'K' },
  ]
  const postfix = postfixes.find((p) => p.len <= len)?.char ?? "";
  let precision = 0;

  if (len >= 12) {
    value /= 10 ** 9;
  } else if (len >= 3) {
    value /= (10 ** (len - len % 3));
    precision = 2 - len % 3;
  }
  return (value).toFixed(precision) + postfix;
}

const parseSongStat = computed((): string[] => {
  let val = button.value?.song[button.value.songStat]
  switch (button.value?.songStat) {
    case 'duration_seconds':
      val = val as number;
      return ["Duration: ", `${Math.floor(val / 60)}:${(val % 60).toString().padStart(2, "0")}`]
    case 'release_date':
      val = val as string;
      return ["Release Date: ", dayjs(val).format("MMM D YYYY")];
    case 'views_on_spotify':
      val = val as number;
      return ["Streams: ", formatStreams(val)]
    default:
      return [":(", "this shouldn't happen"]
  }
})

const albumCoverUrl = computed((): string => {
  return `https://localhost:8001/storage/${
    button.value?.song.album_name_clean
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
      <v-card-title v-text="button?.song.name" />
      <v-card-subtitle class="mt-n2" v-text="button?.song.artist_name" />

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