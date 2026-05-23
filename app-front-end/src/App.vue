<script setup lang="ts">
import HeaderBar from '@/general/HeaderBar.vue';
import SecondaryHeaderBar from '@/general/SecondaryHeaderBar.vue';
</script>

<template>
  <div class="h-[100dvh] flex flex-col justify-center bg-gradient-to-br from-background to-card">
    <div class="h-[60px]">
      <HeaderBar />
    </div>
    <div class="grow flex justify-center min-h-0 px-5 pt-0 overflow-y-scroll">
      <div class="flex flex-col gap-5 max-w-[900px] w-full">
        <SecondaryHeaderBar />
        <RouterView class="grow-0" />
      </div>
    </div>
  </div>
</template>
