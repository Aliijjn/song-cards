<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-vue-next';
import { useRoute } from 'vue-router';

const route = useRoute();

const uuid = computed(() => route.params.uuid);

function download() {
  window.open(`https://localhost:8001/api/downloads/${uuid.value}`, '_blank');
}
</script>

<template>
  <div class="gap-5 max-w-[900px] flex flex-col flex-1">
    <div class="flex justify-between items-center">
      <div class="text-3xl">Your Cards (PDF)</div>
      <Button v-if="uuid" size="lg" @click="download">
        <Download />
        Download
      </Button>
    </div>
    <div class="flex flex-col flex-1 pb-5">
      <span v-if="!uuid">Failed to load PDF</span>
      <iframe
        v-else
        :src="`https://localhost:8001/storage/${uuid}.pdf`"
        class="rounded-[0.75rem] border grow"
      />
    </div>
  </div>
</template>
