<script setup lang="ts">
import {Card, CardDescription, CardTitle} from "@/components/ui/card";
import { useSongStore } from "@/stores/songStore.ts";
import {type SongCardState, type SongDTO} from "@/src/types/types";
import { ref, computed } from "vue";

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
  <Card class="p-0 gap-0">
    <div class="p-5 flex flex-row justify-between align-center">
      <div class="flex flex-col gap-3">
        <CardTitle>{{ selectedSong.name }}</CardTitle>
        <CardDescription>{{ selectedSong.artist_name.join(', ') }}</CardDescription>
      </div>
      <card-title
          v-if="status !== 'idle'"
          :class="[status === 'correct' ? 'text-green-500' : 'text-red-500']"
      >
        {{ comparisonResult }}
      </card-title>
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