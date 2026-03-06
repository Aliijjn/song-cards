<script setup lang="ts">
import {Button} from "@/components/ui/button";
import { Grid2X2Plus } from "lucide-vue-next";

function auth() {
  window.location.href = 'https://127.0.0.1:8001/api/auth';
}
</script>

<template>
  <div class="flex flex-col gap-20 py-5">
    <span class="text-5xl"> Song Cards </span>

    <div class="flex justify-between items-center gap-20">
      <div class="flex flex-col gap-2">
        <span class="text-3xl">Custom Cards</span>
        <span>Create custom song cards from your own playlists. Requires Spotify login</span>
      </div>
      <Button size="lg" class="p-6 rounded-full" @click="auth">
        <Grid2X2Plus />
        Create Custom Cards
      </Button>
    </div>
  </div>
</template>