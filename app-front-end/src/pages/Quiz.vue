<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useSongStore } from "../store.ts";
import ChoiceButton from '../components/ChoiceButton.vue';
import {
  type SongDTO,
  type GuessableStatKey,
  guessableStatKeys,
  type ButtonData,
  type GameData,
} from '../types/types';

//=============================================================================

const store = useSongStore();

const gameData = ref<GameData>({
  score: 0,
  selectedButton: null
})
const songs = ref<SongDTO[]>([]);
const loading = ref<boolean>(true)
const button1 = ref<ButtonData | null>(null)
const button2 = ref<ButtonData | null>(null)

const questions: Record<GuessableStatKey, string> = {
  duration_seconds: "Which song is <strong>longer</strong>?",
  release_date:     "Which song is <strong>newer</strong>?",
  views_on_spotify: "Which song has the <strong>most streams</strong> on Spotify?",
} 

function setButtons() {
  if (!songs.value.length) return;

  let [i1, i2] = [0, 0]

  do {
    i1 = Math.floor(Math.random() * songs.value.length);
    i2 = Math.floor(Math.random() * songs.value.length);
  } while (i1 === i2)

  const key = guessableStatKeys[Math.floor(Math.random() * guessableStatKeys.length)]
  button1.value = {
    song:     songs.value[i1],
    songStat: key,
    index:    0,
    state:    'default',
  }
    button2.value = {
    song:     songs.value[i2],
    songStat: key,
    index:    1,
    state:    'default',
  }
}

function clickButton(selectedButton: number | undefined) {
  console.log(selectedButton)
  if (!button1.value || !button2.value || selectedButton === undefined) {
    return
  }
  const [b1, b2] = [button1.value, button2.value]
  console.log(b1.song, b2.song)

  const selectedSong = selectedButton === 0 ? b1 : b2
  const selectedValue = selectedSong.song[selectedSong.songStat]
  const highestValue = (b1.song[b1.songStat] > b2.song[b2.songStat] ? b1.song[b1.songStat] : b2.song[b2.songStat])
  if (selectedValue === highestValue) {
    gameData.value.score++
    button1.value.state = 'correct'
    button2.value.state = 'correct'
    setTimeout(() => {
      setButtons()
    }, 2000)
  } else {
    store.highScore = Math.max(store.highScore, gameData.value.score)
    store.prevScore = gameData.value.score
    button1.value.state = 'incorrect'
    button2.value.state = 'incorrect'
    setTimeout(() => {
      store.gameState = "lost"
    }, 4000)
  }
}

async function fetchData() {
  const args = store.selectedGenreId === null
    ? ""
    : `?genre_id=${store.selectedGenreId.toString()}`;
  console.log(args, store.selectedGenreId)
  const response = await fetch(
      `https://127.0.0.1:8001/api/songs${args}`
  )
  if (response.ok) {
    songs.value = await response.json()
  }
}

onMounted(() => {
  fetchData()
})

watch(songs, () => {
  setButtons()
  loading.value = false
})

</script>

<template>
  <v-card-title v-if="loading" title="Loading..." />
  <v-card-title v-else-if="!songs.length" title="shit broke D:" />
  <template v-else>
    <v-card-title class="mb-2 text-h5" v-html="button1?.songStat ? questions[button1?.songStat] : 'default'"/>
    <v-card-text class="mx-n4">
      <v-row no-gutters>
        <choice-button :button="button1" @click="clickButton(button1?.index)" />
        <choice-button :button="button2" @click="clickButton(button2?.index)" />
      </v-row>
      <v-card-subtitle class="pt-4 text-right" v-text="`score: ${gameData.score}`" />
    </v-card-text>
  </template>
</template>

