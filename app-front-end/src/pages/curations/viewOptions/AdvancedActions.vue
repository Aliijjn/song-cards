<script setup lang="ts">
import { Copy, Merge, Trash2, QrCode } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import ExportErrorDialog from '@/pages/curations/components/ExportErrorDialog.vue';
import CopyCurationDialog from '@/pages/curations/components/CopyCurationDialog.vue';
import { computed, ref } from 'vue';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';
import CombineCurationDialog from '@/pages/curations/components/CombineCurationDialog.vue';

const curation = defineModel<CurationDTO>({ required: true });
const isLoading = defineModel<boolean>('isLoading', { required: true });

const isExporting = ref(false);
const isCopyModalOpen = ref(false);
const isCombineModalOpen = ref(false);
const isExportDialogOpen = ref(false);

const songsWithErrorCount = computed(
  () => curation.value.songs.filter((s) => s.errors.length > 0).length
);

async function deleteCuration() {
  if (!curation.value) return;

  const response = await CurationAPI.delete(curation.value.id);

  if (response.status === 'success') {
    router.push('/curations');
  }
}

function handleExportClick() {
  if (songsWithErrorCount.value > 0) {
    isExportDialogOpen.value = true;
  } else {
    runExport();
  }
}

async function runExport(skipErrors?: boolean) {
  isExporting.value = true;
  const response = await CurationAPI.toExport(curation.value.id, skipErrors);
  if (response.status === 'success') {
    router.push(`/cards/preview/${response.value}`);
  }
  isExporting.value = false;
}
</script>

<template>
  <CopyCurationDialog v-model:is-open="isCopyModalOpen" :curation="curation" />
  <CombineCurationDialog v-model:is-open="isCombineModalOpen" :current-curation-id="curation.id" />

  <ExportErrorDialog
    v-model:is-open="isExportDialogOpen"
    :error-count="songsWithErrorCount"
    @export="runExport"
  />

  <div class="flex flex-col gap-9 mt-2">
    <div class="flex flex-col gap-3">
      <div class="flex gap-3 items-center">
        <span class="font-headline whitespace-nowrap">Export</span>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <span class="font-medium">Export To Song Cards</span>
          <span class="text-muted-foreground"
            >Turn this curation into a PDF you can print and cut out</span
          >
        </div>
        <Button size="lg" :disabled="isExporting" @click="handleExportClick">
          <QrCode />
          Export
        </Button>
      </div>
    </div>

    <div class="flex flex-col gap-3">
      <div class="flex gap-3 items-center">
        <span class="font-headline whitespace-nowrap">Actions</span>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <span class="font-medium">Duplicate Curation</span>
          <span class="text-muted-foreground">Create a copy of this curation</span>
        </div>
        <Button size="lg" @click="isCopyModalOpen = true">
          <Copy />
          Duplicate
        </Button>
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <span class="font-medium">Combine With Other Curations</span>
          <span class="text-muted-foreground"
            >Combine this curation with multiple other curation</span
          >
        </div>
        <Button size="lg" @click="isCombineModalOpen = true">
          <Merge />
          Combine
        </Button>
      </div>
    </div>

    <div class="flex flex-col gap-3">
      <div class="flex gap-3 items-center">
        <span class="font-headline whitespace-nowrap">Danger Zone</span>
        <Separator class="flex-1" />
      </div>

      <div class="flex justify-between items-center gap-9">
        <div class="flex flex-col">
          <span class="font-medium">Delete Curation</span>
          <span class="text-muted-foreground">This action cannot be undone</span>
        </div>
        <Button variant="destructive" size="lg" @click="deleteCuration">
          <Trash2 />
          Delete
        </Button>
      </div>
    </div>
  </div>
</template>
