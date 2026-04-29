<script setup lang="ts">

import {Button} from "@/components/ui/button";
import {Table, TableBody, TableCell, TableEmpty, TableRow} from "@/components/ui/table";
import {Checkbox} from "@/components/ui/checkbox";
import {Pencil} from "lucide-vue-next";
import {Spinner} from "@/components/ui/spinner";
import type {SongCardDTO} from "@/types/types.ts";
import {ref} from "vue";
import EditSongCardDialog from "@/pages/cards/components/EditSongCardDialog.vue";

const songs = defineModel<SongCardDTO[]>();
const isLoading = defineModel<boolean>('isLoading');
const selected = defineModel<SongCardDTO[]>('selected');

const isEditing = ref(false)
const editedCardIndex = ref<number | null>(null)

function toggleSongCard(songCard: SongCardDTO) {
  if (isLoading.value) return;

  if (selected?.findIndex((sc) => sc.id === songCard.id) !== -1) {
    selected = selected.filter((sc) => sc.id !== songCard.id);
  } else {
    selected.push(songCard);
  }
}

function editSongCard(index: number) {
  editedCardIndex.value = index;
  isEditing.value = true
}
</script>

<template>
  <EditSongCardDialog
      v-if="editedCardIndex !== null"
      v-model="songs[editedCardIndex]"
      v-model:is-open="isEditing"
  />

  <Table>
    <TableEmpty v-if="isLoading">
      <div class="loading-wrapper">
        <Spinner class="size-6" />
        <div> Loading </div>
      </div>
    </TableEmpty>
    <TableEmpty v-else-if="!songs.length">
      No songs found
    </TableEmpty>
    <TableBody v-else>
      <TableRow
          v-for="(song, index) in songs"
          :key="song.id"
          @click="toggleSongCard(song)"
      >
        <TableCell>
          <div class="flex flex-row items-center gap-3">
            <Checkbox
                :id="song.id"
                :model-value="selected.includes(song)"
                :disabled="isLoading"
                class="mx-2"
            />
            <img
                :src="song.imageUrl"
                alt="image"
                class="h-15 w-15 rounded object-cover"
            />
            <div class="flex flex-col truncate">
              <div class="font-bold"> {{ song.name }} </div>
              <div> {{ song.artist }} </div>
            </div>
          </div>
        </TableCell>

        <!-- Year -->
        <TableCell class="pl-12 pr-7">
          <div class="flex justify-end px-3">
          </div>
          {{ song.release_year }}
        </TableCell>

        <!-- Actions -->
        <TableCell class="px-5">
          <Button
              size="icon"
              variant="outline"
              :disabled="isLoading"
              @click="editSongCard(index)"
          >
            <Pencil />
          </Button>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>

<style>
.loading-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.4rem;
}
</style>