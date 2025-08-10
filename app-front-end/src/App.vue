<script setup lang="ts">
import { ref } from 'vue'
import type { SessionData } from './types/types'
import Start from './components/Start.vue'
import Quiz from './components/Quiz.vue'
import Lost from './components/Lost.vue'

const session = ref<SessionData>({
  state: 'start',
  prevScore: -1,
  highScore: -1,
})

</script>

<template>
  <v-app>
    <v-main>
      <div style="max-width: 1000px;" class="w-100 mx-auto">
        <v-card
          class="h-screen"
          rounded="0"
        >
          <Start v-if="session.state === 'start'" :session="session" />
          <Quiz v-else-if="session.state === 'playing'" :session="session" />
          <Lost v-else :session="session" :score="session.prevScore"/>
        </v-card>
      </div>
    </v-main>
  </v-app>
</template>

<style lang="scss">
.default-card {
  padding: 8px;
  margin-bottom: 16px;
}
</style>