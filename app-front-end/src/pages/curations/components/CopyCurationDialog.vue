<script setup lang="ts">
import { watch, computed, ref } from 'vue';
import { type SongCardDTO } from '@/types/types.ts';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { useCloned } from '@vueuse/core';
import { Input } from '@/components/ui/input';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { AlertCircleIcon } from 'lucide-vue-next';
import CurationDTO = App.Data.CurationDTO;
import CurationCopyDTO = App.Data.CurationCopyDTO;
import { Textarea } from '@/components/ui/textarea';
import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';

const { curation } = defineProps<{ curation: CurationDTO | undefined }>();
const isOpen = defineModel<boolean>('isOpen');

const copy = ref<CurationCopyDTO>({
  name: '',
  description: null,
  userId: 1,
  maxSongCount: null,
});

async function apply() {
  const response = await CurationAPI.copy(curation!.id, copy.value);

  if (response.status === 'success') {
    const newId = response.value;
    await router.push(`/curations`);
  }
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <form>
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle> Copy Curation</DialogTitle>
        </DialogHeader>

        <div>
          <Label for="title"> Title </Label>
          <Input id="title" v-model="copy.name" />
        </div>

        <div>
          <Label for="description"> Description <span class="opaque">(optional)</span> </Label>
          <Textarea id="description" v-model="copy.description" />
        </div>

        <div>
          <Label for="song_count">
            Song Count
            <span class="opaque">(optional, limit songs to a smaller, random selection)</span>
          </Label>
          <Input
            id="song_count"
            type="number"
            :min="1"
            :max="curation?.songs?.length"
            v-model.number="copy.maxSongCount"
          />
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
