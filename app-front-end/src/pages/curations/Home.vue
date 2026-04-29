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
  <div class="flex flex-col gap-15 max-w-[900px]">
    <span class="text-5xl"> Curations </span>

    <div class="flex justify-between items-center gap-5">
      <div class="flex flex-col">
        <Label class="text-3xl"> Create Curation </Label>

        Create a custom list of songs to use for song cards or lower higher. Requires Spotify login
      </div>
      <Button size="lg" @click="router.push('/curations/create')">
        <ListPlus />
        Create Curation
      </Button>
    </div>

    <div class="flex flex-col gap-2">
      <div class="flex justify-between items-center gap-5">
        <div class="flex flex-col">
          <Label class="text-3xl"> Recent Curations </Label>
          Your most recent curations
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
