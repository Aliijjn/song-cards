<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import type { PlaylistDTO } from "@/types/types.ts";
import { Table, TableBody, TableCell, TableEmpty, TableRow } from "@/components/ui/table";
import { Checkbox } from "@/components/ui/checkbox";
import { Button } from "@/components/ui/button";
import { useCardStore } from "@/pages/cards/cardStore.ts";
import { router } from "@/router.ts";
import { Input } from "@/components/ui/input";
import { getPlaylists } from "@/pages/cards/api.ts";

const cardStore = useCardStore();

const isLoading = ref(true);
const search = ref('');
const playlists = ref<PlaylistDTO[]>([]);
const isLast = ref(false);

onMounted(async () => {
  const response = await getPlaylists();
  if (response.status === "success") {
    const data = response.value;

    playlists.value.push(...data.playlists);
    isLast.value = data.isLast
  }
  isLoading.value = false;
})

const filteredPlaylists = computed((): PlaylistDTO[] => {
  if (!search.value) return playlists.value;

  const searchResult = search.value.toLowerCase();

  return playlists.value.filter((playlist) => {
    return (
      playlist.name.toLowerCase().includes(searchResult) ||
      playlist.ownerName?.toLowerCase()?.includes(searchResult)
    )
  })
})

function togglePlaylist(id: string) {
  if (cardStore.selectedPlaylists.includes(id)) {
    cardStore.selectedPlaylists = cardStore.selectedPlaylists.filter((p) => p !== id);
  } else {
    cardStore.selectedPlaylists.push(id);
  }
}
</script>

<template>
  <div class="flex flex-col gap-5">
    <div class="flex justify-between items-center">
      <div class="text-3xl">
        Select playlists
      </div>
      <Input v-model="search" placeholder="Search" class="w-60" />
    </div>
    <Table class=" max-w-[720px] table-fixed">
      <TableBody v-if="isLoading">
        <TableRow>
          <TableCell>
            Loading...
          </TableCell>
        </TableRow>
      </TableBody>
      <TableBody v-else>
        <TableEmpty v-if="!filteredPlaylists.length">
          No playlists found
        </TableEmpty>
        <TableRow
            v-for="playlist in filteredPlaylists"
            :key="playlist.id"
            @click="togglePlaylist(playlist.id)"
        >
          <TableCell class="flex flex-row items-center gap-3 max-w-[720px] cursor-pointer">
            <Checkbox
                :id="playlist.id"
                :model-value="cardStore.selectedPlaylists.includes(playlist.id)"
                class="mx-2"
            />
            <img
                :src="playlist.imageUrl"
                alt="image"
                class="h-15 w-15 rounded object-cover"
            />
            <div class="flex flex-col truncate">
              <div class="font-bold"> {{ playlist.name }} </div>
              <div> {{ playlist.ownerName }} </div>
            </div>
            <div class="ml-auto px-3 text-right">
              {{ playlist.songCount }} song{{ playlist.songCount === 1 ? '' : 's' }}
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>

  <div v-if="cardStore.selectedPlaylists.length" class="apply-button">
    <Button size="lg" @click="router.push('verify')">
      Export {{ cardStore.selectedPlaylists.length }} playlist{{ cardStore.selectedPlaylists.length === 1 ? '' : 's'}}
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