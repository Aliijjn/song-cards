<script setup lang="ts">

import { ref, toRef, computed, watch, onMounted } from 'vue';
import {
  type SongData,
  type GuessableStatKey,
  guessableStatKeys,
  type ButtonData,
  type GameData,
  type GameState,
} from '../types/types';
import dayjs from 'dayjs';

const props = defineProps<{
  button: ButtonData | null
}>()
const button = toRef(props, 'button')

const loading = ref(true);

const emit = defineEmits(['click'])

const parseSongStat = computed((): string[] => {
  let val = button.value?.song[button.value.songStat]
  switch (button.value?.songStat) {
    case 'duration_seconds':
      val = val as number;
      return ["Duration: ", `${Math.floor(val / 60)}:${(val % 60).toString().padStart(2, "0")}`]
    case 'release_date':
      val = val as string;
      return ["Release Date: ", dayjs(val).format("MMM D YYYY")];
    default:
      return [":(", "this shouldn't happen"]
  }
})

</script>

<template>
  <v-col cols="12" sm="6">
    <v-card
      class="mb-0 mx-2"
      @click="emit('click', button?.index ?? 0)"
      :loading="button === null"
      :disabled="button?.state !== 'default'"
    >
      <v-card-title :class="`text-${button?.state}`" v-text="button?.song.name" />
      <v-card-subtitle :class="`text-${button?.state}`" v-text="button?.song.artist_name.join(', ')" />

      <v-img
        :src="button?.song.album_cover_url"
        aspect-ratio="1"
        class="ma-2"
        @loadstart="loading = true"
        @load="loading = false"
      />

      <v-card-title
        v-if="button?.state && button?.state !== 'default'"
        :class="`text-${button?.state}`"
      >
        <span>{{ parseSongStat[0] }}</span>
        <span class="font-weight-bold">{{ parseSongStat[1] }}</span>
      </v-card-title>
    </v-card>
  </v-col>
</template>

<style scoped>
</style>