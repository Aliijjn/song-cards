<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-vue-next';
import { useRoute } from 'vue-router';
import Breadcrumbs from '@/general/Breadcrumbs.vue';

const route = useRoute();

const uuid = computed(() => route.params.uuid);

const breadcrumbs = computed(() => [
  {
    text: 'Game Cards',
    to: '/cards',
  },
  {
    text: 'Download',
  },
]);

function download() {
  window.open(`https://localhost:8001/api/downloads/${uuid.value}`, '_blank');
}
</script>

<template>
  <breadcrumbs :breadcrumbs="breadcrumbs" />
  <div class="gap-4 max-w-[900px] h-full flex flex-col">
    <div class="flex justify-between items-center">
      <div class="text-2xl font-light">Your Cards (PDF)</div>
      <Button v-if="uuid" size="lg" @click="download">
        <Download />
        Download
      </Button>
    </div>
    <div class="flex flex-col grow pb-5">
      <span v-if="!uuid">Failed to load PDF</span>
      <iframe
        v-else
        :src="`https://localhost:8001/storage/${uuid}.pdf`"
        class="rounded-md border grow"
      />
    </div>
  </div>
</template>
