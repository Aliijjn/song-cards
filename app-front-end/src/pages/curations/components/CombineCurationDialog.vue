<script setup lang="ts">
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import CurationCombineDTO = App.Data.CurationCombineDTO;
import { onMounted, ref } from 'vue';
import { Switch } from '@/components/ui/switch';
import CurationTable from '@/pages/curations/components/CurationTable.vue';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';

const isOpen = defineModel<boolean>('isOpen');
const { currentCurationId } = defineProps<{ currentCurationId: string }>();

const combine = ref<CurationCombineDTO>({
  name: '',
  description: '',
  userId: 1,
  keepOriginal: true,
  curationIds: [],
});
const curations = ref<CurationDTO[]>([]);
const isLoading = ref<boolean>(true);

onMounted(async () => {
  const response = await CurationAPI.getMultiple();

  if (response.status === 'success') {
    curations.value = response.value.filter((curation) => curation.id !== currentCurationId);
  }
  isLoading.value = false;
});

async function apply() {
  const response = await CurationAPI.combine(currentCurationId, combine.value);

  if (response.status === 'success') {
    const newCurationId = response.value;

    router.push(`/curations/${newCurationId}`);
  }
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <form>
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle> Curation Data</DialogTitle>
        </DialogHeader>

        <div>
          <Label for="title"> Title </Label>
          <Input id="title" v-model="combine.name" />
        </div>

        <div>
          <Label for="description"> Description <span class="opaque">(optional)</span> </Label>
          <Textarea id="description" v-model="combine.description" />
        </div>

        <div class="flex flex-col">
          <Label for="song_count"> Keep Original </Label>
          <Switch v-model="combine.keepOriginal" />
        </div>

        <DialogHeader>
          <DialogTitle> Curations To Add</DialogTitle>
        </DialogHeader>

        <curation-table
          v-model:selected="combine.curationIds"
          :curations="curations"
          :is-loading="isLoading"
        />

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
