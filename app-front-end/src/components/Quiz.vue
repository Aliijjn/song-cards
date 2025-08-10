<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import ChoiceButton from './ChoiceButton.vue';
import {
  type SongData,
  type GuessableStatKey,
  guessableStatKeys,
  type ButtonData,
  type GameData,
  type GameState,
  type SessionData,
} from '../types/types';

const session = defineModel<SessionData>('session');

const gameData = ref<GameData>({
  score: 0,
  selectedButton: null
})
const songs = ref<SongData[]>([]);
const loading = ref<boolean>(true)
const button1 = ref<ButtonData | null>(null)
const button2 = ref<ButtonData | null>(null)

const questions: Record<GuessableStatKey, string> = {
  song_duration: "Which song is <strong>longer</strong>?",
  release_date: "Which song is <strong>newer</strong>?",
  total_views_on_spotify: "Which song has the <strong>most streams</strong> on Spotify?",
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
  const highestValue = Math.max(b1.song[b1.songStat], b2.song[b2.songStat])
  if (selectedValue === highestValue) {
    gameData.value.score++
    button1.value.state = 'correct'
    button2.value.state = 'correct'
    setTimeout(() => {
      setButtons()
    }, 2000)
  } else {
    session.value!.highScore = Math.max(session.value!.highScore, gameData.value.score)
    session.value!.prevScore = gameData.value.score
    button1.value.state = 'incorrect'
    button2.value.state = 'incorrect'
    setTimeout(() => {
      session.value!.state = "lost"
    }, 4000)
  }
}

async function fetchData() {
  const response = await fetch('http://localhost:8080/api/songs')
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
    <v-card-text>
      <v-row>
        <ChoiceButton :button="button1" @click="clickButton(button1?.index)" />
        <ChoiceButton :button="button2" @click="clickButton(button2?.index)" />
      </v-row>
      <v-card-subtitle class="pt-2 text-right" v-text="`score: ${gameData.score}`" />
    </v-card-text>
  </template>
</template>

<style scoped>

</style>
