<script setup lang="ts">
import HeaderBar from "@/general/HeaderBar.vue";
</script>

<template>
  <div class="h-[100dvh] flex flex-col">
    <HeaderBar />
    <div class="flex flex-1 justify-center pb-5">
      <RouterView />
    </div>
  </div>
</template>