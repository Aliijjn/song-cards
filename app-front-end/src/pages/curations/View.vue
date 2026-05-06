<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import CurationDTO = App.Data.CurationDTO;
import CurationAPI from '@/pages/curations/api.ts';
import { router } from '@/router.ts';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import CurationDetails from '@/pages/curations/viewOptions/CurationDetails.vue';
import AdvancedActions from '@/pages/curations/viewOptions/AdvancedActions.vue';
import SongDetails from '@/pages/curations/viewOptions/SongDetails.vue';
import Breadcrumbs, { type Breadcrumb } from '@/general/Breadcrumbs.vue';

type Tabs = 'curation_details' | 'song_details' | 'advanced_actions';
const route = useRoute();

const selectedTab = ref<Tabs>('curation_details');
const curation = ref<CurationDTO>();
const isLoading = ref(true);

const uuid = computed(() => route.params.uuid as string);

const breadcrumbs = computed<Breadcrumb[]>(() => [
  {
    text: 'Curations',
    to: '/curations',
  },
  {
    text: curation.value?.name ?? '',
  },
]);

watch(uuid, load, { immediate: true });

async function load() {
  isLoading.value = true;
  const response = await CurationAPI.get(uuid.value);
  if (response.status === 'success') {
    curation.value = response.value;
  } else {
    router.push('/404');
  }

  selectedTab.value = 'curation_details';
  isLoading.value = false;
}
</script>

<template>
  <Breadcrumbs :breadcrumbs="breadcrumbs" />
  <div class="w-full">
    <span v-if="isLoading && !curation"> Loading </span>
    <span v-else-if="!curation"> Couldn't get curation </span>
    <Tabs v-else v-model="selectedTab">
      <TabsList class="mb-6 w-full h-10">
        <TabsTrigger class="w-full" value="curation_details"> Curation Details</TabsTrigger>
        <TabsTrigger class="w-full" value="song_details"> Song Details</TabsTrigger>
        <TabsTrigger class="w-full" value="advanced_actions"> Advanced Actions</TabsTrigger>
      </TabsList>

      <TabsContent value="curation_details">
        <CurationDetails v-model="curation" v-model:isLoading="isLoading" />
      </TabsContent>

      <TabsContent value="song_details">
        <SongDetails v-model="curation" v-model:isLoading="isLoading" @reload="load" />
      </TabsContent>

      <TabsContent value="advanced_actions">
        <AdvancedActions v-model="curation" v-model:isLoading="isLoading" />
      </TabsContent>
    </Tabs>
  </div>
</template>
