<script setup lang="ts">
const { text } = defineProps<{ text: string }>();
</script>

<template>
  <div class="d-flex flex-row align-center">
    <v-divider />
    <span class="text-textDivider mx-2"> {{ text }} </span>
    <v-divider />
  </div>
</template>