<script setup lang="ts">
import { computed } from 'vue';
import { useSongStore } from "../store.ts";

//=============================================================================

const store = useSongStore();

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
    quote: "Ain't nothin' gonna break my stride, nobody gonna slow me down, oh no!",
    artist: "Matthew Wilder",
    song: "Break My Stride",
    year: "1983"
  },
]

const getQuote = computed(() => {
  for (let i = quotes.length - 1; i >= 0; i--) {
    if (store.prevScore >= quotes[i].score) {
      return ([
        `"${quotes[i].quote}"`,
        `- ${quotes[i].artist}, "${quotes[i].song}" (${quotes[i].year})`
      ])
    }
  }
  return ["Error loading cheesy quote", ":("]
})

</script>

<template>
  <div class="ma-4">
    <div class="d-flex flex-row">
      <div class="ml-2 mb-4 mr-1 quote_prefix"></div>
      <v-card-subtitle class="pt-2 d-flex flex-column">
        <span class="font-italic text-h6" v-text="getQuote[0]"></span>
        <span class="font-italic" v-text="getQuote[1]"></span>
      </v-card-subtitle>
    </div>

    <v-card-subtitle class="mx-0 my-2" v-text="`Score: ${ store.prevScore }`" />
    <v-card-subtitle class="mx-0 my-2" v-text="`High score: ${ store.highScore }`" />

    <v-btn
        variant="tonal"
        elevation="4"
        @click="store.gameState='start'"
        color="correct"
        text="Try Again"
        size="large"
    />
  </div>
</template>

<style scoped lang="css">
.quote_prefix {
  width: 0.25rem;
  height: 3.5rem;
  margin-right: 8px;
  background-color: #777777;
}
</style>