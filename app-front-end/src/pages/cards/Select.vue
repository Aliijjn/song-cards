<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { useCardStore } from '@/pages/cards/cardStore.ts';
import { router } from '@/router.ts';
import SearchBar from '@/general/SearchBar.vue';
import SelectPlaylists from '@/pages/cards/components/SelectPlaylists.vue';

const cardStore = useCardStore();

const search = ref('');
</script>

<template>
  <div class="flex flex-col gap-5">
    <div class="flex justify-between items-center">
      <div class="text-3xl">Select playlists</div>
      <SearchBar v-model="search" />
    </div>
    <SelectPlaylists v-model="cardStore.selectedPlaylists" :search="search" />
  </div>

  <div v-if="cardStore.selectedPlaylists.length" class="apply-button">
    <Button size="lg" @click="router.push('verify')">
      Export {{ cardStore.selectedPlaylists.length }} playlist
      {{ cardStore.selectedPlaylists.length === 1 ? '' : 's' }}
    </Button>
  </div>
</template>

<style scoped lang="css">
.apply-button {
  position: fixed;
  bottom: 1rem;
  margin: 0 auto;
}
</style>
