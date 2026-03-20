<script setup lang="ts">
import { Card } from "@/components/ui/card";
import { useSongStore } from "@/stores/songStore.ts";
import { type SongCardState} from "@/types/types";
import { computed } from "vue";

import SongDTO = App.Data.SongDTO

// ============================================================================

const songStore = useSongStore();

const { index, status } = defineProps<{
  index: number;
  status: SongCardState;
}>()
// const emit = defineEmits(['click'])

const selectedSong = computed((): SongDTO => songStore.currentSongs![index])
const comparisonResult = computed(
    () => songStore.currentComparison.formatFn(
        selectedSong.value[songStore.currentComparison.type]
    )
)

</script>

<template>
  <Card class="p-0 gap-0 overflow-hidden">
    <div class="p-5 flex flex-row justify-between items-center">
      <div class="flex flex-col gap-1">
        <div class="text-2xl">{{ selectedSong.name }}</div>
        <div class="text-xl text-muted-foreground">{{ selectedSong.artist_name.join(', ') }}</div>
      </div>
      <div
          v-if="status !== 'idle'"
          class="text-2xl"
          :class="[status === 'correct' ? 'text-green-500' : 'text-red-500']"
      >
        {{ comparisonResult }}
      </div>
    </div>
    <img
        :src="selectedSong.album_cover_url"
        :alt="`${selectedSong.album_name} by ${selectedSong.artist_name}`"
    />
  </Card>
</template>

<style scoped lang="css">
img {
  aspect-ratio: 1;
  object-fit: cover;
}
</style>