<script setup lang="ts">

import { useSongStore } from "../store.ts";
import DifficultySelector from "./DifficultySelector.vue";

const store = useSongStore();

function startMatch() {
  store.gameState = 'playing';
}

function cancel() {
  store.startMatchModal = false;
}

</script>

<template>
  <v-dialog v-model="store.startMatchModal" max-width="500">
    <v-card>
      <v-card-title v-text="store.selectedGenre?.name ?? 'Quick Match'" />
      <v-card-text class="text-textMuted" v-text="store.selectedGenre?.description" />
      <difficulty-selector />
      <v-divider />

      <v-row no-gutters class="mx-1 mb-2">
        <v-spacer />
        <v-col cols="3">
          <div class="px-1">
            <v-btn
                @click="cancel()"
                elevation="0"
                variant="text"
                color="custom"
                v-text="'Cancel'"
                block
            />
          </div>
        </v-col>
        <v-col cols="3">
          <div class="px-1">
            <v-btn
              @click="startMatch()"
              variant="flat"
              color="custom"
              v-text="'Start'"
              block
            />
          </div>
        </v-col>
      </v-row>
    </v-card>
  </v-dialog>
</template>