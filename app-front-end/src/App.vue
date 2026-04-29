<script setup lang="ts">
import HeaderBar from '@/general/HeaderBar.vue';
import { Card } from '@/components/ui/card';
</script>

<template>
  <div class="h-[100dvh] flex flex-col bg-gradient-to-br from-background to-card">
    <HeaderBar />
    <div class="flex flex-1 justify-center p-5 overflow-y-scroll">
      <RouterView />
    </div>
  </div>
</template>
