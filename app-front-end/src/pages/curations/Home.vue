<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ref, onMounted } from 'vue';
import { ListPlus } from 'lucide-vue-next';
import CurationAPI from '@/pages/curations/api.ts';
import Breadcrumbs from '@/general/Breadcrumbs.vue';
import SearchBar from '@/general/SearchBar.vue';
import CreateCurationDialog from '@/pages/curations/components/CreateCurationDialog.vue';
import CurationCardRow from '@/pages/curations/components/CurationCardRow.vue';

const personal = ref<App.Data.CurationDTO[]>([]);
const editorial = ref<App.Data.CurationDTO[]>([]);
const era = ref<App.Data.CurationDTO[]>([]);
const isLoading = ref(true);
const search = ref('');
const creatingNew = ref(false);

onMounted(async () => {
  const response = await CurationAPI.getMultiple();
  if (response.status === 'success') {
    personal.value = response.value.personal;
    editorial.value = response.value.editorial;
    era.value = response.value.era;
  }
  isLoading.value = false;
});
</script>

<template>
  <Breadcrumbs :breadcrumbs="[{ text: 'Curations' }]" />

  <CreateCurationDialog v-model:is-open="creatingNew" />

  <div class="flex flex-col gap-8 pb-5 max-w-[900px]">
    <div class="flex justify-between gap-2">
      <SearchBar v-model="search" size="lg" class="w-full" />
      <Button size="lg" @click="creatingNew = true">
        <ListPlus />
        Create Curation
      </Button>
    </div>

    <CurationCardRow :curations="personal" label="Your Curations" :search="search" :is-loading="isLoading" />
    <CurationCardRow :curations="editorial" label="Editorial" :search="search" :is-loading="isLoading" />
    <CurationCardRow :curations="era" label="Eras" :search="search" :is-loading="isLoading" />
  </div>
</template>
