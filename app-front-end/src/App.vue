<script setup lang="ts">
import HeaderBar from "@/src/general/HeaderBar.vue";
import Home from "@/src/pages/home/Home.vue";
import {useBaseStore} from "@/stores/baseStore.ts";
import Game from "@/src/pages/game/Game.vue";
import Lost from "@/src/pages/lost/Lost.vue";

// ============================================================================

const baseStore = useBaseStore();
</script>

<template>
  <HeaderBar />
  <div class="flex justify-center bg-background px-5 pb-5">
    <div style="max-width: 1080px">
      <Home v-if="baseStore.gameState === 'start'" />
      <Game v-else-if="baseStore.gameState === 'playing'" />
      <Lost v-else-if="baseStore.gameState === 'lost'" />
      <span v-else> This shouldn't ever happen </span>
    </div>
  </div>
</template>
