<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import CurationCreationFromSongsDTO = App.Data.CurationCreationFromSongsDTO;

const isOpen = defineModel<boolean>('isOpen');
const { curationId, songIds } = defineProps<{
  curationId: string;
  songIds: Set<string>;
}>();

const isLoading = ref(false);
const curation = ref<Omit<CurationCreationFromSongsDTO, 'songIds'>>({
  name: '',
  description: '',
  userId: 1,
});

async function create() {
  isLoading.value = true;
  const response = await CurationAPI.createFromSongs(curationId, {
    ...curation.value,
    songIds: Array.from(songIds),
  });

  if (response.status === 'success') {
    router.push(`/curations/${response.value}`);
  }
  isLoading.value = false;
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <form>
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle> New Curation</DialogTitle>
        </DialogHeader>
        <div>
          <Label for="title">Title</Label>
          <Input v-model="curation.name" id="title" />
        </div>

        <div>
          <Label for="description">
            Description
            <span class="text-muted-foreground text-sm">(optional)</span>
          </Label>
          <Textarea v-model="curation.description" id="description" />
        </div>

        <DialogFooter>
          <DialogClose as-child>
            <Button variant="outline" @click="isOpen = false"> Cancel</Button>
          </DialogClose>
          <Button @click="create" :disabled="isLoading"> Apply</Button>
        </DialogFooter>
      </DialogContent>
    </form>
  </Dialog>
</template>
