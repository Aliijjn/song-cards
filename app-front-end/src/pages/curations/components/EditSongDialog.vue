<script setup lang="ts">
import { watch, computed } from 'vue';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { useCloned } from '@vueuse/core';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircleIcon, ChevronDownIcon } from 'lucide-vue-next';
import SongDTO = App.Data.SongDTO;
import dayjs from 'dayjs';
import { useDebounceFn } from '@vueuse/core';
import CurationAPI from '@/pages/curations/api.ts';

const song = defineModel<SongDTO | null>({ required: true });
const isOpen = defineModel<boolean>('isOpen');
const { curationId } = defineProps<{
  curationId: string;
}>();
const emit = defineEmits(['reload']);

const { cloned: tempSong, sync } = useCloned(song, { manual: true });

watch(isOpen, (nv) => nv && sync());

const errors = computed(() => song?.value?.errors?.map((err) => errorLookup.get(err)));

const errorLookup = new Map<number, string>([
  [1, 'Song is likely a rerelease. Release date may be inaccurate'],
  [2, 'Song is likely a live song. Release date may be inaccurate'],
]);

const setDate = useDebounceFn((unit: dayjs.UnitType, value: number) => {
  const newDate = dayjs(tempSong.value?.release_date).set(unit, value);

  console.log(newDate, tempSong.value?.release_date, unit, value, newDate.isValid());

  if (newDate.isValid()) {
    tempSong.value!.release_date = newDate.toISOString();
  }
}, 1000);

async function apply() {
  const response = await CurationAPI.putSongEdit(curationId, {
    song_id: song.value!.id,
    name: tempSong.value!.name,
    release_date: tempSong.value!.release_date,
  });

  if (response.status === 'success') {
    emit('reload');
    isOpen.value = false;
  }
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <form>
      <DialogContent class="sm:max-w-[600px]" v-if="tempSong">
        <DialogHeader>
          <DialogTitle> Edit song card</DialogTitle>
        </DialogHeader>

        <Alert v-if="errors?.length">
          <AlertCircleIcon />
          <AlertTitle>Potential issues with song card</AlertTitle>
          <AlertDescription>
            <ul class="mt-2 list-inside list-disc space-y-1">
              <li v-for="(err, i) in errors" :key="i">{{ err }}</li>
            </ul>
          </AlertDescription>
        </Alert>

        <div>
          <Label for="title"> Title </Label>
          <Input id="title" v-model="tempSong.name" />
        </div>

        <div class="grid grid-cols-[1fr_1fr_1fr] gap-2">
          <div>
            <Label for="release_year"> Release Year </Label>
            <Input
              id="release_year"
              :model-value.number="dayjs(tempSong.release_date).year()"
              @update:model-value="(val: number) => setDate('year', val)"
            />
          </div>

          <div>
            <Label for="release_month"> Month </Label>
            <Input
              id="release_month"
              :model-value.number="dayjs(tempSong.release_date).month() + 1"
              @update:model-value="(val: number) => setDate('month', val - 1)"
            />
          </div>

          <div>
            <Label for="release_day"> Day </Label>
            <Input
              id="release_day"
              :model-value.number="dayjs(tempSong.release_date).date()"
              @update:model-value="(val: number) => setDate('date', val)"
            />
          </div>
        </div>

        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" @click="isOpen = false"> Cancel</Button>
          </DialogClose>
          <Button @click="apply"> Apply</Button>
        </DialogFooter>
      </DialogContent>
    </form>
  </Dialog>
</template>
