<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useCardStore } from '@/pages/cards/cardStore.ts';
import type { SongCardDTO } from '@/types/types.ts';
import { Button } from '@/components/ui/button';
import { router } from '@/router.ts';
import ExportAPI from '@/pages/cards/api.ts';
import SelectSongs from '@/pages/cards/components/SelectSongs.vue';

const cardStore = useCardStore();

const isLoading = ref(true);
const validCards = ref<SongCardDTO[]>([]);
const invalidCards = ref<SongCardDTO[]>([]);

onMounted(async () => {
  const response = await ExportAPI.getCardData(cardStore.selectedPlaylists);
  if (response.status === 'success') {
    const { valid, invalid } = response.value;

    validCards.value = valid;
    invalidCards.value = invalid;
    cardStore.selectedSongCards = JSON.parse(JSON.stringify(validCards.value));
  } else {
    console.error('Unable to get card data', response.message);
  }
  isLoading.value = false;
});

async function startExport() {
  isLoading.value = true;
  const response = await ExportAPI.exportCardData(cardStore.selectedSongCards);
  if (response.status === 'success') {
    const uuid = response.value;
    router.push(`/cards/preview/${uuid}`);
  }
  isLoading.value = false;
}
</script>

<template>
  <div class="flex flex-col gap-5 w-[900px]">
    <div class="flex justify-between">
      <div>
        <div class="flex text-3xl">Verify Data</div>
        <div>
          These songs likely have some invalid data. You can manually edit and include them before
          continuing
        </div>
      </div>
    </div>

    <SelectSongs
      v-model="invalidCards"
      v-model:is-loading="isLoading"
      v-model:selected="cardStore.selectedSongCards"
    />
  </div>

  <div class="apply-button">
    <Button size="lg" :disabled="isLoading" @click="startExport"> Create Cards</Button>
  </div>
</template>

<style scoped lang="css">
.apply-button {
  position: fixed;
  bottom: 1rem;
  margin: 0 auto;
}
</style>
