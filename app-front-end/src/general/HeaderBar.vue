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
  <div class="flex justify-between items-center p-3 pl-4">
    <Logo />

    <div class="flex items-center gap-3">
      <div class="flex gap-1">
        <Button
          v-for="(tab, i) in tabs"
          :key="i"
          variant="ghost"
          class="text-base"
          @click="router.push(tab.to)"
        >
          {{ tab.text }}
        </Button>
      </div>
      <UserIcon />
    </div>
  </div>
  <Separator />
</template>
