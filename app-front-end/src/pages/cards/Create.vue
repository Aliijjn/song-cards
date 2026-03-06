<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useCardStore } from "@/pages/cards/cardStore.ts";
import { Card, CardAction, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Download } from 'lucide-vue-next';

type CardResponseDTO = {
  preview: string;
  download: string;
}

const cardStore = useCardStore();

const isLoading = ref(true)
const links = ref<CardResponseDTO | null>(null)

onMounted(async () => {
  const response = await fetch("https://localhost:8001/api/cards/create", {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      card_data: cardStore.selectedSongCards,
    }),
  });
  if (response.ok) {
    links.value = (await response.json()) as CardResponseDTO;
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
      <Button v-if="links" size="lg" class="h-12 rounded-full" @click="download">
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
          class="rounded-2xl border grow"
      />
    </div>
  </div>
</template>