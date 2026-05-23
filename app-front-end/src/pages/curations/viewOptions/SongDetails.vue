<script setup lang="ts">
import dayjs from 'dayjs';
import { CircleAlert, Pencil, Trash2, Funnel } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableEmpty, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import SongDTO = App.Data.SongDTO;
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import EditSongDialog from '@/pages/curations/components/EditSongDialog.vue';
import { Input } from '@/components/ui/input';
import SearchBar from '@/general/SearchBar.vue';
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
} from '@/components/ui/dropdown-menu';
import SelectInput from '@/general/SelectInput.vue';
import { NumberField } from '@/components/ui/number-field';

const curation = defineModel<CurationDTO>({ required: true });
const isLoading = defineModel<boolean>('isLoading', { required: true });
const emit = defineEmits(['reload']);

const isSongEditOpen = ref(false);
const selectedSong = ref<SongDTO | null>(null);
const maxVisible = ref(50);
const search = ref('');
const errorFilter = ref<boolean | null>(null);
const yearFilter = ref<{ after: boolean | null; year: number }>({
  after: null,
  year: 2000,
});

const loadMoreTrigger = ref<HTMLElement | null>(null);
let observer: IntersectionObserver | null = null;

const filteredSongs = computed(() => {
  const lower = search.value.toLowerCase();

  return curation.value.songs.filter(
    (song) =>
      (!search.value ||
        song.name?.toLowerCase().includes(lower) ||
        song.artist_name?.some((artist) => artist.toLowerCase().includes(lower))) &&
      (errorFilter.value === null || !!song.errors.length === errorFilter.value) &&
      (yearFilter.value.after === null ||
        dayjs(song.release_date).year() >= yearFilter.value.year === yearFilter.value.after)
  );
});
const visibleSongs = computed(() => filteredSongs.value.slice(0, maxVisible.value));

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

onMounted(() => {
  observer = new IntersectionObserver(
    ([entry]) => {
      if (entry?.isIntersecting && maxVisible.value < curation.value.songs.length) {
        maxVisible.value += 50;
      }
    },
    {
      root: null,
      rootMargin: '2000px',
      threshold: 0,
    }
  );

  if (loadMoreTrigger.value) {
    observer.observe(loadMoreTrigger.value);
  }
});

onBeforeUnmount(() => {
  observer?.disconnect();
});
</script>

<template>
  <EditSongDialog
    v-model="selectedSong"
    v-model:is-open="isSongEditOpen"
    :curation-id="curation.id"
    @reload="emit('reload')"
  />

  <div class="flex gap-2 mb-5">
    <SearchBar v-model="search" size="lg" class="w-full" />
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button size="lg" variant="outline">
          <Funnel />
          Filters
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent class="flex flex-col gap-3 p-4 w-80" align="end">
        <div>
          <Label for="error_filter"> Errors </Label>
          <SelectInput
            v-model="errorFilter"
            :options="[
              { label: 'All songs', value: null },
              { label: 'Without errors', value: false },
              { label: 'With errors', value: true },
            ]"
            id="error_filter"
            class="w-full"
          />
        </div>
        <div>
          <Label for="time_filter"> Time </Label>
          <div class="grid grid-cols-2 gap-2">
            <SelectInput
              v-model="yearFilter.after"
              :options="[
                { label: 'None', value: null },
                { label: 'Before', value: false },
                { label: 'After / During', value: true },
              ]"
              id="time_filter"
              class="w-full"
            />
            <Input
              v-model.number="yearFilter.year"
              id="time_filter_2"
              :disabled="yearFilter.after === null"
            />
          </div>
        </div>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>

  <Table>
    <TableEmpty v-if="!filteredSongs.length"> No songs found</TableEmpty>
    <TableBody v-else>
      <TableRow v-for="(song, index) in visibleSongs" :key="song.id">
        <!-- Image, Title & Artist -->
        <TableCell class="max-w-[60%]">
          <div class="flex flex-row items-center gap-3 max-w-[min(500px,50vw)]">
            <img :src="song.albumCoverUrl" alt="" class="h-15 w-15 rounded object-cover" />
            <div class="flex flex-col truncate">
              <div class="font-medium">{{ song.name }}</div>
              <div class="opaque">{{ song.artist_name.join(', ') }}</div>
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
  <div v-if="maxVisible < filteredSongs.length" ref="loadMoreTrigger" class="h-2" />
</template>
