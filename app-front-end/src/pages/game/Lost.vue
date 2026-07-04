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
  <div class="flex flex-col gap-8">
    <div>
      <div v-html="quote.quote" class="text-4xl" />
      <div v-html="quote.info" class="font-headline text-muted-foreground" />
    </div>

    <div class="text-muted-foreground text-xl">
      <div> Points scored: {{ baseStore.prevScore }} </div>
      <div> High Score: {{ baseStore.highScore }} </div>
    </div>

    <div class="flex gap-4">
      <Button size="lg" class="w-40 h-12" @click="router.push('/game')">
        <Repeat />
        Play Again
      </Button>
      <Button variant="outline" size="lg" class="w-40 h-12" @click="router.push('/')">
        <CornerDownLeft />
        Back To Menu
      </Button>
    </div>
  </div>
</template>