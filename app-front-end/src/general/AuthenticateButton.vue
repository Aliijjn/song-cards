<script setup lang="ts">
import { auth } from '@/general/auth.ts';
import { Button } from '@/components/ui/button';
import { Key, Check } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import { getUserSpotifyTokenValidity } from '@/pages/cards/api.ts';
import dayjs from 'dayjs';
import { Spinner } from '@/components/ui/spinner';

const spotifyTokenValidity = ref<dayjs.Dayjs | null>(null);
const isLoading = ref(true);

onMounted(async () => {
  const response = await getUserSpotifyTokenValidity();
  if (response.status === 'success') {
    spotifyTokenValidity.value = dayjs(response.value).tz('UTC', true);
  }
  isLoading.value = false;
});
</script>

<template>
  <div class="flex items-center !gap-1" @click="auth">
    <Spinner v-if="isLoading" size="6" />
    <template v-else-if="spotifyTokenValidity === null || dayjs().isAfter(spotifyTokenValidity)">
      <Key />
      Authenticate
    </template>
    <template v-else>
      <Check />
      Authenticated
    </template>
  </div>
</template>
