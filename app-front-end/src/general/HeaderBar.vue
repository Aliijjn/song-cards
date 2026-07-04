<script setup lang="ts">
import { router } from '@/router.ts';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import UserIcon from '@/general/UserIcon.vue';
import Logo from '@/general/Logo.vue';

const tabs = [
  {
    text: 'Higher Lower',
    to: '/',
  },
  {
    text: 'Game Cards',
    to: '/cards',
  },
  {
    text: 'Curations',
    to: '/curations',
  },
];
</script>

<template>
  <div class="h-15 shadow-blue-500">
    <div class="grid grid-cols-3 items-center pl-3 p-2">
      <Logo />

      <div class="flex justify-center items-center gap-1">
        <Button v-for="(tab, i) in tabs" :key="i" variant="ghost" @click="router.push(tab.to)">
          {{ tab.text }}
        </Button>
      </div>

      <div class="flex justify-end">
        <UserIcon />
      </div>
    </div>
    <Separator />
  </div>
</template>
