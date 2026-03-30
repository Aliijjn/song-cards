<script setup lang="ts">
import ExportTable from "@/pages/cards/components/ExportTable.vue";
</script>

<template>
  <div class="flex flex-col gap-5">
    <span class="text-3xl"> All Exports </span>
    <ExportTable :start="0" :length="10" show-footer />
  </div>
</template>