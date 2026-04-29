<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Grid2X2Plus, List } from 'lucide-vue-next';
import ExportTable from '@/pages/cards/components/ExportTable.vue';
import { router } from '@/router.ts';
import { auth } from '@/general/auth';
</script>

<template>
  <div class="flex flex-col gap-15">
    <span class="text-5xl"> Song Cards </span>

    <div class="flex justify-between items-center gap-20">
      <div class="flex flex-col gap-1">
        <span class="text-3xl">Custom Cards</span>
        <span> Create custom song cards from your own playlists. Requires Spotify login </span>
      </div>
      <Button size="lg" class="!px-6 h-12" @click="router.push('/cards/select')">
        <Grid2X2Plus />
        Create Custom Cards
      </Button>
    </div>

    <div class="flex flex-col gap-5">
      <div class="flex flex-row justify-between items-center gap-2">
        <div class="flex flex-col gap-1">
          <div class="text-3xl">Recent Cards</div>
          <div>Your most recent card exports, downloadable for 30 days after creation</div>
        </div>
        <Button size="lg" class="!px-6 h-12" @click="router.push('cards/previous-exports')">
          <List />
          Show More
        </Button>
      </div>
      <ExportTable />
    </div>
  </div>
</template>
