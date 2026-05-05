import { defineStore } from 'pinia';
import { onMounted, ref, computed } from 'vue';

import dayjs from 'dayjs';
import ExportAPI from '@/pages/cards/api.ts';

export const useGlobalStore = defineStore('globalStore', () => {
  const userSpotifyTokenValidity = ref<dayjs.Dayjs | null>(null);
  const isUserSpotifyTokenValid = computed(
    () =>
      userSpotifyTokenValidity.value === null || dayjs().isBefore(userSpotifyTokenValidity.value)
  );

  async function getUserSpotifyTokenValidity() {
    const response = await ExportAPI.getUserSpotifyTokenValidity();
    if (response.status === 'success') {
      userSpotifyTokenValidity.value = dayjs(response.value).tz('UTC', true);
    }
  }

  onMounted(getUserSpotifyTokenValidity);

  return {
    userSpotifyTokenValidity,
    isUserSpotifyTokenValid,
    getUserSpotifyTokenValidity,
  };
});
