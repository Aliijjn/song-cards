<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import {
  Card,
  CardAction,
  CardTitle,
} from '@/components/ui/card'

import { useBaseStore } from "@/stores/baseStore.ts";
import type { GenreDTO } from "@/src/types/types.ts";
import StartMatchModal from "@/src/pages/home/StartMatchModal.vue";

//=============================================================================

const store = useBaseStore();

onMounted(async () => {
  await store.fetchGenres();
})

function openStartMatchModal(genre: GenreDTO | null): void {
  store.startMatchModal = true;
  store.selectedGenre = genre;
}
</script>

<template>
  <StartMatchModal />
  <div class="flex flex-col gap-5">
    <Card class="px-5">
      <CardTitle>Welcome to Spotify Higher-Lower!</CardTitle>
      <CardAction class="d-flex flex-col">
        <Button @click="openStartMatchModal(null)"> Quick March </Button>
      </CardAction>
    </Card>
    <Card class="px-5">
      <CardTitle>Genres</CardTitle>
      <CardAction class="grid grid-cols-4 gap-5">
        <Card v-for="genre in store?.genres" :key="genre.id" class="p-0 gap-0" @click="openStartMatchModal(genre)">
          <span class="p-3 m-0 overflow-scroll">{{ genre.name }}</span>
          <img :src="genre.showcased_album" :alt="genre.name" />
        </Card>
      </CardAction>
    </Card>
  </div>
</template>

<style scoped lang="css">
img {
  aspect-ratio: 1;
  object-fit: cover;
}
</style>