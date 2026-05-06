<script setup lang="ts">
import { router } from '@/router.ts';
import dayjs from 'dayjs';
import { ArrowRight, QrCode } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableEmpty, TableRow } from '@/components/ui/table';
import CurationDTO = App.Data.CurationDTO;
import { ref } from 'vue';
import CurationAPI from '@/pages/curations/api.ts';
import { Checkbox } from '@/components/ui/checkbox';

const selected = defineModel<string[]>('selected', { default: null });
const {
  curations,
  isLoading = false,
  showActions = false,
} = defineProps<{
  curations: CurationDTO[];
  isLoading?: boolean;
  showActions?: boolean;
}>();

const isExporting = ref(false);

async function runExport(curationId: string) {
  isExporting.value = true;
  const response = await CurationAPI.toExport(curationId);
  if (response.status === 'success') {
    const exportId = response.value;

    router.push(`/cards/preview/${exportId}`);
  }
  isExporting.value = false;
}

function toggle(curationId: string) {
  if (selected.value === null) return;
  if (selected.value.includes(curationId)) {
    selected.value = selected.value.filter((p) => p !== curationId);
  } else {
    selected.value.push(curationId);
  }
}
</script>

<template>
  <Table class="table-fixed">
    <TableBody v-if="isLoading">
      <TableRow>
        <TableCell> Loading...</TableCell>
      </TableRow>
    </TableBody>
    <TableBody v-else>
      <TableEmpty v-if="!curations.length"> No curations found</TableEmpty>
      <TableRow v-for="curation in curations" :key="curation.id" @click="toggle(curation.id)">
        <TableCell v-if="selected !== null">
          <Checkbox :model-value="selected.includes(curation.id)" />
        </TableCell>
        <TableCell class="flex flex-row items-center gap-3">
          <div class="flex flex-col truncate">
            <div>{{ curation.name }}</div>
            <div class="opaque">{{ curation.createdBy }}</div>
          </div>
        </TableCell>
        <TableCell>
          {{ curation.songs.length }} song{{ curation.songs.length === 1 ? '' : 's' }}
        </TableCell>
        <TableCell>
          {{ dayjs(curation.updatedAt).format('MMMM DD YYYY HH:mm') }}
        </TableCell>
        <TableCell v-if="showActions">
          <div class="flex justify-end gap-2">
            <Button
              size="icon"
              variant="outline"
              :disabled="isExporting"
              @click="runExport(curation.id)"
            >
              <QrCode />
            </Button>
            <Button size="icon" variant="outline" @click="router.push(`/curations/${curation.id}`)">
              <ArrowRight />
            </Button>
          </div>
        </TableCell>
      </TableRow>
    </TableBody>
  </Table>
</template>
