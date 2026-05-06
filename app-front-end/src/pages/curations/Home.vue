<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ref, onMounted } from 'vue';
import { ListPlus, TextAlignJustify } from 'lucide-vue-next';
import { router } from '@/router.ts';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import CurationTable from '@/pages/curations/components/CurationTable.vue';
import Breadcrumbs from '@/general/Breadcrumbs.vue';

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
  <Breadcrumbs :breadcrumbs="[{ text: 'Curations' }]" />

  <div class="flex flex-col gap-9 max-w-[900px]">
    <div class="flex justify-between items-center gap-20">
      <div class="flex flex-col">
        <Label class="text-subtitle"> Your Curations </Label>
        <span class="opaque">
          A curation is a collection of songs, that can be used to make game cards or to play higher
          lower. Requires Spotify login to create
        </span>
      </div>
      <Button size="lg" @click="router.push('/curations/create')">
        <ListPlus />
        Create Curation
      </Button>
    </div>

    <div class="flex flex-col gap-2">
      <div class="flex justify-between items-center gap-5">
        <div class="flex flex-col">
          <Label class="text-subtitle"> Recent Curations </Label>
        </div>
        <Button size="lg" @click="router.push('/curations/all')">
          <TextAlignJustify />
          Show All
        </Button>
      </div>
      <CurationTable :curations="recentCurations" :is-loading="isLoading" show-actions />
    </div>
  </div>
</template>
