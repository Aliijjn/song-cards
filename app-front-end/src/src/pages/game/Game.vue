<script setup lang="ts">
import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { ref, onMounted } from "vue";
import { useBaseStore } from "@/stores/baseStore.ts";
import { useSongStore } from "@/stores/songStore.ts";
import SongCard from "@/src/pages/game/SongCard.vue";
import type { SongCardState } from "@/src/types/types.ts";

// ============================================================================

const baseStore = useBaseStore()
const songStore = useSongStore()

const currentScore = ref(0)
const songCardState = ref<SongCardState>('idle')

onMounted(async () => {
  await songStore.getSongs()
})

async function clickCard(index: number) {
  if (songCardState.value !== "idle") return

  const isCorrect = songStore.isCorrectCard(index)

  if (isCorrect) {
    currentScore.value++
    songCardState.value = 'correct'
    setTimeout(() => {
      songCardState.value = 'idle'
      songStore.increment()
    }, 2000)
  } else {
    baseStore.prevScore = currentScore.value;
    currentScore.value = 0;
    baseStore.highScore = Math.max(baseStore.highScore, baseStore.prevScore);
    songCardState.value = 'incorrect'
    setTimeout(() => {
      baseStore.gameState = 'lost'
    }, 3000)
  }
}
</script>

<template>
  <div class="flex flex-col">
    <CardTitle v-html="songStore.currentComparison?.description" class="mb-5" />
    <CardHeader v-if="songStore.currentSongs === null"> No songs available, please try again </CardHeader>
    <div v-else class="flex flex-row gap-5">
      <song-card v-for="(song, i) in songStore.currentSongs" :key="i" :index="i" :status="songCardState" @click="clickCard(i)" />
    </div>
    <div class="flex justify-end mt-3">
      <CardDescription>
        Score: {{ currentScore }}
      </CardDescription>
    </div>
  </div>
</template>