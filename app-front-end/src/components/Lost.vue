<script setup lang="ts">
import { computed } from 'vue';
import type { SessionData } from './../types/types'

const session = defineModel<SessionData>('session');
const props = defineProps<{score: number}>()

const quotes: { score: number, quote: string, song: string, artist: string, year: string }[] = [
  {
    score: 0,
    quote: "Hello darkness, my old friend...",
    artist: "Simon & Garfunkel",
    song: "The Sound of Silence",
    year: "1964"
  },
  {
    score: 1,
    quote: "One is the loneliest number that you'll ever do",
    artist: "Three Dog Night",
    song: "One",
    year: "1969"
  },
  {
    score: 2,
    quote: "And you may ask yourself, well, how did I get here?",
    artist: "Talking Heads",
    song: "Once in a Lifetime",
    year: "1980"
  },
  {
    score: 3,
    quote: "All in all you're just another brick in the wall",
    artist: "Pink Floyd",
    song: "Another Brick in the Wall",
    year: "1979"
  },
  {
    score: 5,
    quote: "Meet the new boss, same as the old boss",
    artist: "The Who",
    song: "Won't Get Fooled Again",
    year: "1971"
  },
  {
    score: 10,
    quote: "Ain't nothin'gonna break my stride, nobody gonna slow me down, oh no!",
    artist: "Matthew Wilder",
    song: "Break My Stride",
    year: "1983"
  },
]

const getQuote = computed(() => {
  for (let i = quotes.length - 1; i >= 0; i--) {
    if (props.score >= quotes[i].score) {
      return ([
        `"${quotes[i].quote}"`,
        `- ${quotes[i].artist}, "${quotes[i].song}" (${quotes[i].year})`
      ])
    }
  }
  return ["Error loading cheezy quote", ":("]
})

</script>

<template>
  <div class="mt-4 d-flex flex-row">
    <div class="ml-4 mb-4 mr-n1 quote"></div>
    <v-card-subtitle class="d-flex flex-column">
    <span class="font-italic text-h6" v-text="getQuote[0]"></span>
    <span class="font-italic" v-text="getQuote[1]"></span>
    </v-card-subtitle>
  </div>

  <v-card-subtitle v-text="`Score: ${ session?.prevScore }`" />
  <v-card-subtitle v-text="`High score: ${ session?.highScore }`" />

  <v-btn
    class="pa-2 mt-4 ml-4"
    variant="tonal"
    elevation="4"
    @click="session!.state='start'"
    color="correct"
    text="Try Again"
    size="large"
  />
</template>

<style scoped lang="css">
.quote {
  width: 0.25rem;
  height: 3.5rem;
  margin-right: 8px;
  background-color: #777777;
}
</style>