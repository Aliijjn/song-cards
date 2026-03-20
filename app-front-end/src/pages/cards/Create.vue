<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useCardStore } from "@/pages/cards/cardStore.ts";
import { Button } from "@/components/ui/button";
import { Download } from 'lucide-vue-next';
import {exportCardData} from "@/pages/cards/api.ts";

type CardResponseDTO = {
  preview: string;
  download: string;
}

const cardStore = useCardStore();

const isLoading = ref(true)
const links = ref<CardResponseDTO | null>(null)

onMounted(async () => {
  const response = await exportCardData(cardStore.selectedSongCards)
  if (response.status === "success") {
    links.value = response.value;
  }
  isLoading.value = false
});

function download() {
  window.open(links.value?.download, '_blank')
}
</script>

<template>
  <div class="gap-5 max-w-[720px] w-full flex flex-col flex-1">
    <div class="flex justify-between items-center">
      <div class="text-3xl">Your Cards (PDF)</div>
      <Button v-if="links" size="lg" class="!px-6 h-12" @click="download">
        <Download />
        Download
      </Button>
    </div>
    <div class="w-full flex flex-col flex-1">
      <span v-if="isLoading">Loading...</span>
      <span v-else-if="!links">Failed to load PDF</span>
      <iframe
          v-else
          :src="'https://localhost:8001' + links?.preview"
          class="rounded-[0.75rem] border grow"
      />
    </div>
  </div>
</template>