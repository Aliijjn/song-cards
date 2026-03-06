import { defineStore } from "pinia";
import { ref } from "vue";
import type { SongCardDTO } from "@/types/types.ts";

export const useCardStore = defineStore("cardStore", () => {
    const selectedPlaylists = ref<string[]>([]);
    const selectedSongCards = ref<SongCardDTO[]>([]);

    return {
        selectedPlaylists,
        selectedSongCards
    };
});
