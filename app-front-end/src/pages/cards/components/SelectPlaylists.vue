<script setup lang="ts">
import { Table, TableBody, TableCell, TableEmpty, TableRow } from '@/components/ui/table';
import { Checkbox } from '@/components/ui/checkbox';
import { computed, onMounted, ref } from 'vue';
import type { PlaylistDTO } from '@/types/types.ts';
import { getPlaylists } from '@/pages/cards/api.ts';

const selectedPlaylists = defineModel<string[]>();
const isLoading = defineModel<boolean>('isLoading', { default: true });
const { search } = defineProps<{
  search?: string;
}>();

const playlists = ref<PlaylistDTO[]>([]);
const isLast = ref(false);

onMounted(async () => {
  isLoading.value = true;
  const response = await getPlaylists();
  if (response.status === 'success') {
    const data = response.value;

    playlists.value.push(...data.playlists);
    isLast.value = data.isLast;
  }
  isLoading.value = false;
});

const filteredPlaylists = computed((): PlaylistDTO[] => {
  if (!search) return playlists.value;

  const searchResult = search.toLowerCase();

  return playlists.value.filter((playlist) => {
    return (
      playlist.name.toLowerCase().includes(searchResult) ||
      playlist.ownerName?.toLowerCase()?.includes(searchResult)
    );
  });
});

function togglePlaylist(id: string) {
  if (selectedPlaylists.value.includes(id)) {
    selectedPlaylists.value = selectedPlaylists.value.filter((p) => p !== id);
  } else {
    selectedPlaylists.value.push(id);
  }
}
</script>

<template>
  <Table class="w-[900px] table-fixed">
    <TableBody v-if="isLoading">
      <TableRow>
        <TableCell> Loading...</TableCell>
      </TableRow>
    </TableBody>
    <TableBody v-else>
      <TableEmpty v-if="!filteredPlaylists.length"> No playlists found</TableEmpty>
      <TableRow
        v-for="playlist in filteredPlaylists"
        :key="playlist.id"
        @click="togglePlaylist(playlist.id)"
      >
        <TableCell class="flex flex-row items-center gap-3 max-w-[720px] cursor-pointer">
          <Checkbox
            :id="playlist.id"
            :model-value="selectedPlaylists.includes(playlist.id)"
            class="mx-2"
          />
          <img :src="playlist.imageUrl" alt="image" class="h-15 w-15 rounded object-cover" />
          <div class="flex flex-col truncate">
            <div class="font-bold">{{ playlist.name }}</div>
            <div>{{ playlist.ownerName }}</div>
          </div>
          <div class="ml-auto px-3 text-right">
            {{ playlist.songCount }} song{{ playlist.songCount === 1 ? '' : 's' }}
          </div>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
