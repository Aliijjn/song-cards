<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { useBaseStore } from "@/stores/baseStore.ts";
import { Card, CardDescription, CardFooter, CardTitle } from "@/components/ui/card";
import { getQuote } from "@/src/pages/lost/getQuote.ts";

const baseStore = useBaseStore();
const quote = getQuote(baseStore.prevScore);
</script>

<template>
  <Card>
    <div class="px-5">
      <div class="flex flex-col pb-5">
        <div v-html="quote.quote" />
        <CardDescription v-html="quote.info" />
      </div>
      <CardDescription>
        Points scored: {{ baseStore.prevScore }}
      </CardDescription>
      <CardDescription>
        High Score: {{ baseStore.highScore }}
      </CardDescription>
    </div>

    <CardFooter>
      <Button @click="baseStore.gameState = 'start'">
        Back to menu
      </Button>
    </CardFooter>
  </Card>
</template>