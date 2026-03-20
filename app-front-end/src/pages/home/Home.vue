<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import { useBaseStore } from "@/stores/baseStore.ts";
import StartMatchModal from "@/pages/home/StartMatchModal.vue";
import { Input } from "@/components/ui/input";
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "@/components/ui/carousel";
import { Play, FastForward, Search } from 'lucide-vue-next';
import { router } from "@/router.ts";

import GenreDTO = App.Data.GenreDTO;

//=============================================================================

const store = useBaseStore();

const genreFilter = ref("");
const visibleGenres = computed(() => {
  const filter = genreFilter.value.toLowerCase();

  return store.genres.filter((genre) => genre.name.toLowerCase().includes(filter))
});

onMounted(async () => {
  await store.fetchGenres();
})

function openStartMatchModal(genre: GenreDTO | null): void {
  store.startMatchModal = true;
  store.selectedGenre = genre;
}
</script>

<template>
  <StartMatchModal />
  <div class="flex flex-col gap-20 w-[1080px]">
    <span class="text-5xl"> Higher-Lower </span>

    <div class="flex justify-between items-center">
      <div class="flex flex-col gap-2">
        <span class="text-3xl">Quick Match</span>
        <span>Quickly jump into a game of Higher Lower. Features a wide range of popular songs, spanning many decades</span>
      </div>
      <Button size="lg" class="!px-6 h-12" @click="router.push('/game')">
        <FastForward />
        Quick Match
      </Button>
    </div>

    <div class="flex flex-col gap-5">
      <div class="flex justify-between items-center">
        <span class="text-3xl">Genres</span>
        <Input v-model="genreFilter" placeholder="Search" class="w-60 pl-4">
          <Search />
        </Input>
      </div>
      <Carousel>
        <CarouselContent>
          <CarouselItem v-for="genre in visibleGenres" :key="genre.id" class="sm:basis-1 md:basis-1/2 lg:basis-1/4">
            <div class="flex flex-col items-center gap-3 cursor-pointer" @click="openStartMatchModal(genre)">
              <img :src="genre.showcased_album" :alt="genre.name" class="rounded-[0.75rem]" />
              <span class="text-ellipsis">{{ genre.name }}</span>
              <Button class="!px-4">
                <Play />
                Play
              </Button>
            </div>
          </CarouselItem>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
      </Carousel>
    </div>
  </div>
</template>

<style scoped lang="css">
img {
  aspect-ratio: 1;
  object-fit: cover;
}

</style>