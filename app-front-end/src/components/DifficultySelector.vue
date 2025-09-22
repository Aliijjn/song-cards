<script setup lang="ts">

import { computed, ref } from 'vue';
import { useSongStore } from "../store.ts";

const store = useSongStore();

const difficulties: { name: string; value: number }[] = [
  { name: "easy",   value: 75 },
  { name: "medium", value: 60 },
  { name: "hard",   value: 40 },
]
const difficultyTicks = difficulties.reduce((acc, d) => {
  acc[d.value] = d.name;
  return acc;
}, {} as Record<number, string>);

const usesCustomDifficulty = computed(() => {
  return difficulties.find((d) => d.value === store.difficulty) === undefined;
})
const customDifficulty = ref(false);
function toggleCustomDifficulty() {
  customDifficulty.value = !customDifficulty.value;
}

</script>

<template>
  <v-card-title class="text-subtitle-1" v-text="'Difficulty'" />
  <v-row no-gutters class="mx-1">
    <template v-if="customDifficulty">
      <v-slider
        v-model="store.difficulty"
        :ticks="difficultyTicks"
        thumb
        density="compact"
        class="mx-5 mb-n5"
        step="1"
        :min="0"
        :max="85"
      />
    </template>
    <template v-else>
      <v-col v-for="difficulty in difficulties" :key="difficulty.value" cols="3">
        <div class="px-1">
          <v-btn
              @click="store.difficulty = difficulty.value"
              :variant="difficulty.value === store.difficulty ? 'flat' : 'outlined'"
              :color="difficulty.name"
              v-text="difficulty.name"
              height="40"
              block
          />
        </div>
      </v-col>
    </template>
    <v-col cols="3">
      <div class="px-1">
        <v-btn
          @click="toggleCustomDifficulty()"
          :variant="usesCustomDifficulty || customDifficulty ? 'flat' : 'outlined'"
          color="custom"
          v-text="customDifficulty ? 'apply' : 'custom'"
          block
        />
      </div>
    </v-col>
  </v-row>
</template>