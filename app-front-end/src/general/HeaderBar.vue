<script setup lang="ts">
import { computed } from 'vue'
import { useColorMode } from "@vueuse/core";
import { Switch } from "@/components/ui/switch";
import { Moon, Sun } from 'lucide-vue-next';
import { router } from "@/router.ts";

const theme = computed({
  get: () => useColorMode().value === 'dark',
  set: (val: boolean) => useColorMode().value = val ? 'dark' : 'light',
})
</script>

<template>
  <div class="grid grid-cols-3 p-3 gap-3">
    <div>

    </div>
    <div class="flex justify-center gap-10">
      <div class="cursor-pointer" @click="router.push('/')"> Higher Lower </div>
      <div class="cursor-pointer" @click="router.push('/cards')"> Game Cards </div>
      <div class="cursor-pointer" @click="router.push('/curations')"> Curations </div>
    </div>
    <div class="flex justify-end items-center gap-2">
      <Sun v-if="!theme" class="h-5 w-5" />
      <Moon v-else class="h-5 w-5" />
      <Switch v-model="theme" />
    </div>
  </div>
</template>

