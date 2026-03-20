<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useBaseStore } from "@/stores/baseStore.ts";
import { useSongStore } from "@/stores/songStore.ts";
import SongCard from "@/pages/game/SongCard.vue";
import type { SongCardState } from "@/types/types.ts";
import { router } from "@/router.ts";

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
      router.push('/game/lost')
    }, 3000)
  }
}
</script>

<template>
  <div class="flex flex-col">
    <span v-html="songStore.currentComparison?.description" class="mb-8 text-4xl" />

    <span v-if="songStore.currentSongs === null" class="text-xl">
      No songs available, please try again
    </span>
    <div v-else class="flex flex-row gap-[2em]">
      <song-card v-for="(_, i) in songStore.currentSongs" :key="i" :index="i" :status="songCardState" @click="clickCard(i)" />
    </div>

    <div class="text-end text-xl text-muted-foreground mt-4">
      Score: {{ currentScore }}
    </div>
  </div>
</template>