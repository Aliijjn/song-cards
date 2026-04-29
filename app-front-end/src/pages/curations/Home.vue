<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ref, onMounted } from 'vue';
import { ListPlus, TextAlignJustify } from 'lucide-vue-next';
import { router } from '@/router.ts';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import CurationTable from '@/pages/curations/components/CurationTable.vue';

const recentCurations = ref<CurationDTO[]>([]);
const isLoading = ref(true);

onMounted(async () => {
  const response = await CurationAPI.getMultiple();
  if (response.status === 'success') {
    recentCurations.value = response.value;
  }
  isLoading.value = false;
});
</script>

<template>
  <div class="flex flex-col gap-10 max-w-[900px]">
    <span class="text-5xl"> Curations </span>

    <div class="flex justify-between items-center gap-5">
      <div class="flex flex-col">
        <Label> Create Curation </Label>
        <span class="opaque"
          >Create a custom list of songs to use for song cards or lower higher. Requires Spotify
          login</span
        >
      </div>
      <Button size="lg" @click="router.push('/curations/create')">
        <ListPlus />
        Create Curation
      </Button>
    </div>

    <div class="flex flex-col gap-2">
      <div class="flex justify-between items-center gap-5">
        <div class="flex flex-col">
          <Label> Recent Curations </Label>
          <span class="opaque"> Your most recent curations </span>
        </div>
        <Button size="lg" @click="router.push('/curations/all')">
          <TextAlignJustify />
          Show All
        </Button>
      </div>
      <CurationTable :curations="recentCurations" :is-loading="isLoading" />
    </div>
  </div>
</template>
