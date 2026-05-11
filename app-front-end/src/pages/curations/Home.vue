<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ref, onMounted, computed } from 'vue';
import { ListPlus } from 'lucide-vue-next';
import { router } from '@/router.ts';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import CurationTable from '@/pages/curations/components/CurationTable.vue';
import Breadcrumbs from '@/general/Breadcrumbs.vue';
import SearchBar from '@/general/SearchBar.vue';

const curations = ref<CurationDTO[]>([]);
const isLoading = ref(true);
const search = ref('');

const visibleCurations = computed(() => {
  if (!search.value) {
    return curations.value;
  }

  const s = search.value.toLowerCase();

  return curations.value.filter((curation) => curation.name.toLowerCase().includes(s));
});

onMounted(async () => {
  const response = await CurationAPI.getMultiple();
  if (response.status === 'success') {
    curations.value = response.value;
  }
  isLoading.value = false;
});
</script>

<template>
  <Breadcrumbs :breadcrumbs="[{ text: 'Curations' }]" />

  <div class="flex flex-col gap-4 max-w-[900px]">
    <div class="flex justify-between gap-2">
      <SearchBar v-model="search" class="h-[43px] w-full" />
      <Button size="lg" @click="router.push('/curations/create')">
        <ListPlus />
        Create Curation
      </Button>
    </div>

    <CurationTable :curations="visibleCurations" :is-loading="isLoading" show-actions />
  </div>
</template>
