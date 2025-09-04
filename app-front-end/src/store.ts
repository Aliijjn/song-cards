import { defineStore } from "pinia";
import { ref } from "vue";
import type { GenreDO } from "./types/types.ts";

export const useSongStore = defineStore("songStore", () => {
    const genres = ref<GenreDO[]>([]);

    async function fetchGenres() {
        try {
            const response = await fetch("https://127.0.0.1:8001/api/genres");
            if (!response.ok) throw new Error("Failed to fetch genres");
            genres.value = await response.json();
        } catch (err) {
            console.error(err);
        }
    }

    return {
        genres,
        fetchGenres,
    };
});
