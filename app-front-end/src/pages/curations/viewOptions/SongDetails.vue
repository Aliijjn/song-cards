<script setup lang="ts">
import dayjs from 'dayjs';
import { CircleAlert, Pencil, Trash2 } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableEmpty, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import SongDTO = App.Data.SongDTO;
import { ref } from 'vue';
import EditSongDialog from '@/pages/curations/components/EditSongDialog.vue';

const curation = defineModel<CurationDTO>({ required: true });
const isLoading = defineModel<boolean>('isLoading', { required: true });
const emit = defineEmits(['reload']);

const isSongEditOpen = ref(false);
const selectedSong = ref<SongDTO | null>(null);

async function deleteSong(songId: string) {
  if (!curation.value) return;

  const response = await CurationAPI.deleteSong(curation.value.id, songId);

  if (response.status === 'success') {
    curation.value.songs = curation.value.songs.filter((song) => song.id !== songId);
  }
}

function editSong(song: SongDTO) {
  selectedSong.value = song;
  isSongEditOpen.value = true;
}

function getSongErrorCount(count: number) {
  if (count === 0) return '';
  if (count === 1) return '1 error';
  return `${count} errors`;
}
</script>

<template>
  <EditSongDialog
    v-model="selectedSong"
    v-model:is-open="isSongEditOpen"
    :curation-id="curation.id"
    @reload="emit('reload')"
  />

  <Table>
    <TableEmpty v-if="!curation.songs.length"> No songs found</TableEmpty>
    <TableBody v-else>
      <TableRow v-for="(song, index) in curation.songs" :key="song.id">
        <TableCell>
          <div class="flex flex-row items-center gap-3">
            <img :src="song.imageUrl" alt="image" class="h-15 w-15 rounded object-cover" />
            <div class="flex flex-col truncate">
              <div class="font-bold">{{ song.name }}</div>
              <div>{{ song.artist_name.join(', ') }}</div>
            </div>
          </div>
        </TableCell>

        <!-- Year -->
        <TableCell>
          {{ dayjs(song.release_date).year() }}
        </TableCell>

        <!-- Errors -->
        <TableCell>
          <div class="flex items-center justify-end gap-1 text-destructive">
            <CircleAlert v-if="song.errors.length" class="size-3.5 pb-[2px]" />
            {{ getSongErrorCount(song.errors.length) }}
          </div>
        </TableCell>

        <!-- Actions -->
        <TableCell>
          <div class="flex justify-end gap-2">
            <Button size="icon" variant="outline" :disabled="isLoading" @click="editSong(song)">
              <Pencil />
            </Button>
            <Button
              size="icon"
              variant="destructive"
              :disabled="isLoading"
              @click="deleteSong(song.id)"
            >
              <Trash2 />
            </Button>
          </div>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
