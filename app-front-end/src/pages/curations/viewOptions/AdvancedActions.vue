<script setup lang="ts">
import { Copy, Merge, Trash2, QrCode } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import CopyCurationDialog from '@/pages/curations/components/CopyCurationDialog.vue';
import { ref } from 'vue';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';
import CombineCurationDialog from '@/pages/curations/components/CombineCurationDialog.vue';

const curation = defineModel<CurationDTO>({ required: true });
const isLoading = defineModel<boolean>('isLoading', { required: true });

const isExporting = ref(false);
const isCopyModalOpen = ref(false);
const isCombineModalOpen = ref(false);

async function deleteCuration() {
  if (!curation.value) return;

  const response = await CurationAPI.delete(curation.value.id);

  if (response.status === 'success') {
    router.push('/curations');
  }
}

async function runExport() {
  isExporting.value = true;
  const response = await CurationAPI.toExport(curation.value.id);
  if (response.status === 'success') {
    const exportId = response.value;

    router.push(`/cards/preview/${exportId}`);
  }
  isExporting.value = false;
}
</script>

<template>
  <CopyCurationDialog v-model:is-open="isCopyModalOpen" :curation="curation" />
  <CombineCurationDialog v-model:is-open="isCombineModalOpen" :current-curation-id="curation.id" />

  <div class="flex flex-col gap-9 mt-2">
    <div class="flex flex-col gap-3">
      <div class="flex gap-3 items-center">
        <Label class="text-subtitle whitespace-nowrap">Export</Label>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <Label class="font-medium">Export To Song Cards</Label>
          <span class="opaque">Turn this curation into a PDF you can print and cut out</span>
        </div>
        <Button size="lg" :disabled="isExporting" @click="runExport">
          <QrCode />
          Export
        </Button>
      </div>
    </div>

    <div class="flex flex-col gap-3">
      <div class="flex gap-3 items-center">
        <Label class="text-subtitle whitespace-nowrap">Actions</Label>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <Label class="font-medium">Duplicate Curation</Label>
          <span class="opaque">Create a copy of this curation</span>
        </div>
        <Button size="lg" @click="isCopyModalOpen = true">
          <Copy />
          Duplicate
        </Button>
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <Label class="font-medium">Combine With Other Curations</Label>
          <span class="opaque">Combine this curation with multiple other curation</span>
        </div>
        <Button size="lg" @click="isCombineModalOpen = true">
          <Merge />
          Combine
        </Button>
      </div>
    </div>

    <div class="flex flex-col gap-3">
      <div class="flex gap-3 items-center">
        <Label class="text-subtitle whitespace-nowrap">Danger Zone</Label>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <Label class="font-medium">Delete Curation</Label>
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
