<script setup lang="ts">
import { watch, computed } from "vue";
import { type SongCardDTO } from "@/types/types.ts";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader, DialogTitle
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { useCloned } from "@vueuse/core";
import { Input } from "@/components/ui/input";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { AlertCircleIcon } from 'lucide-vue-next'

const song = defineModel<SongCardDTO | null>()
const isOpen = defineModel<boolean>("isOpen");

const { cloned: tempSong, sync } = useCloned(song, { manual: true });

function apply() {
  song.value = tempSong.value;
  console.log(song.value);
  isOpen.value = false;
}

watch(isOpen, (nv) => nv && sync())

const errors = computed(() => song?.value?.errors?.map((err) => errorLookup.get(err)))

const errorLookup = new Map<number, string>([
  [1, "Song is likely a rerelease. Release date may be inaccurate"],
  [2, "Song is likely a live song. Release date may be inaccurate"],
]);
</script>

<template>
  <Dialog v-model:open="isOpen">
    <form>
      <DialogContent class="sm:max-w-[600px]" v-if="tempSong">
        <DialogHeader>
          <DialogTitle>
            Edit song card
          </DialogTitle>
        </DialogHeader>

        <Alert>
          <AlertCircleIcon />
          <AlertTitle>Potential issues with song card</AlertTitle>
          <AlertDescription>
            <ul class="mt-2 list-inside list-disc space-y-1">
              <li v-for="(err, i) in errors" :key="i"> {{ err }} </li>
            </ul>
          </AlertDescription>
        </Alert>

        <div>
          <Label for="title"> Title </Label>
          <Input id="title" v-model="tempSong.name" />
        </div>

        <div>
          <Label for="artist"> Artist(s) </Label>
          <Input id="artist" v-model="tempSong.artist" />
        </div>

        <div>
          <Label for="release_year"> Release Year </Label>
          <Input id="release_year" v-model.number="tempSong.release_year" />
        </div>

        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" @click="isOpen = false">
              Cancel
            </Button>
          </DialogClose>
          <Button @click="apply">
            Apply
          </Button>
        </DialogFooter>
      </DialogContent>
    </form>
  </Dialog>
</template>