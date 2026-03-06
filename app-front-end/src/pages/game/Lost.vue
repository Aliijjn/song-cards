<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { useBaseStore } from "@/stores/baseStore.ts";
import { getQuote } from "@/pages/game/getQuote.ts";
import { router } from "@/router.ts";
import { Repeat, CornerDownLeft } from 'lucide-vue-next';

const baseStore = useBaseStore();
const quote = getQuote(baseStore.prevScore);
</script>

<template>
  <div class="flex flex-col gap-10">
    <div>
      <div v-html="quote.quote" class="text-4xl" />
      <div v-html="quote.info" class="text-2xl opacity-60" />
    </div>

    <div>
      <div class="text-xl"> Points scored: {{ baseStore.prevScore }} </div>
      <div class="text-xl"> High Score: {{ baseStore.highScore }} </div>
    </div>

    <div class="flex gap-3">
      <Button size="lg" class="w-40 h-12 rounded-full" @click="router.push('/game')">
        <Repeat />
        Play Again
      </Button>
      <Button variant="outline" size="lg" class="w-40 h-12 rounded-full" @click="router.push('/')">
        <CornerDownLeft />
        Back To Menu
      </Button>
    </div>
  </div>
</template>