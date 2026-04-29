<script setup lang="ts">
import { Copy, Merge, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import CopyCurationDialog from '@/pages/curations/components/CopyCurationDialog.vue';
import { ref } from 'vue';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';

const curation = defineModel<CurationDTO>({ required: true });
const isLoading = defineModel<boolean>('isLoading', { required: true });

const isCopyModalOpen = ref(false);

async function deleteCuration() {
  if (!curation.value) return;

  const response = await CurationAPI.delete(curation.value.id);

  if (response.status === 'success') {
    router.push('/curations');
  }
}
</script>

<template>
  <CopyCurationDialog v-model:is-open="isCopyModalOpen" :curation="curation" />

  <div class="flex flex-col gap-10">
    <div class="flex flex-col gap-5">
      <div class="flex gap-5 items-center">
        <span class="text-3xl whitespace-nowrap">Actions</span>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-10">
        <div class="flex flex-col">
          <Label>Duplicate Curation</Label>
          <span class="opaque">Create a copy of this curation</span>
        </div>
        <Button size="lg" @click="isCopyModalOpen = true">
          <Copy />
          Duplicate
        </Button>
      </div>

      <div class="flex justify-between items-center gap-10">
        <div class="flex flex-col">
          <Label>Combine With Other Curations</Label>
          <span class="opaque">Combine this curation with multiple other curation</span>
        </div>
        <Button size="lg">
          <Merge />
          Combine
        </Button>
      </div>
    </div>

    <div class="flex flex-col gap-5">
      <div class="flex gap-5 items-center">
        <span class="text-3xl whitespace-nowrap">Danger Zone</span>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-10">
        <div class="flex flex-col">
          <Label>Delete Curation</Label>
          <span class="opaque">This action cannot be undone</span>
        </div>
        <Button variant="destructive" size="lg" @click="deleteCuration">
          <Trash2 />
          Delete
        </Button>
      </div>
    </div>
  </div>
</template>
