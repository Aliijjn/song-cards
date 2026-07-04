<script setup lang="ts">
import { ref, computed } from 'vue';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';
import { router } from '@/router.ts';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import CurationSummaryDTO = App.Data.CurationSummaryDTO;

const ROW_SIZE = 4;

const {
  curations,
  label,
  search = '',
  isLoading = false,
} = defineProps<{
  curations: CurationSummaryDTO[];
  label: string;
  search?: string;
  isLoading?: boolean;
}>();

const expanded = ref(false);

const filtered = computed(() => {
  if (!search) return curations;
  const s = search.toLowerCase();
  return curations.filter((c) => c.name.toLowerCase().includes(s));
});

const visible = computed(() =>
  expanded.value ? filtered.value : filtered.value.slice(0, ROW_SIZE)
);
</script>

<template>
  <div class="flex flex-col">
    <div class="flex flex-row items-center gap-3 mb-1">
      <span class="font-headline whitespace-nowrap">{{ label }}</span>
      <Separator class="flex-1" />
    </div>
    <div v-if="isLoading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
      <Card
        v-for="i in ROW_SIZE"
        :key="i"
        class="rounded-md overflow-hidden p-0 gap-0 animate-pulse"
      >
        <div class="w-full aspect-square bg-muted" />
        <CardContent class="flex flex-col gap-2 p-3">
          <div class="h-3.5 w-3/4 rounded bg-muted" />
          <div class="h-3 w-1/3 rounded bg-muted" />
        </CardContent>
      </Card>
    </div>
    <div v-else-if="visible.length === 0" class="flex justify-center items-center h-30 opaque">
      No curations found
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
      <Card
        v-for="curation in visible"
        :key="curation.id"
        class="rounded-md cursor-pointer overflow-hidden p-0 gap-0 transition-shadow hover:shadow-md"
        @click="router.push(`/curations/${curation.id}`)"
      >
        <img
          v-if="curation.coverUrl"
          :src="curation.coverUrl"
          :alt="curation.name"
          class="w-full aspect-square object-cover"
        />
        <div v-else class="w-full aspect-square bg-muted" />
        <CardContent class="flex flex-col text-sm p-3">
          <span class="font-medium truncate">{{ curation.name }}</span>
          <span class="opaque"
            >{{ curation.songCount }} song{{ curation.songCount === 1 ? '' : 's' }}</span
          >
        </CardContent>
      </Card>
    </div>
    <div class="min-h-11">
      <Button
        v-if="filtered.length > ROW_SIZE"
        variant="ghost"
        size="sm"
        class="mt-2"
        @click="expanded = !expanded"
      >
        <ChevronUp v-if="expanded" />
        <ChevronDown v-else />
        {{ expanded ? 'Show less' : `Show ${filtered.length - ROW_SIZE} more` }}
      </Button>
    </div>
  </div>
</template>
