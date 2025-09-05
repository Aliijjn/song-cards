import { defineStore } from "pinia";
import { ref } from "vue";
import type {GameState, GenreDO} from "./types/types.ts";

export const useSongStore = defineStore("songStore", () => {
    const gameState = ref<GameState>('start');
    const prevScore = ref<number>(0);
    const highScore = ref<number>(0);

    const genres = ref<GenreDO[]>([]);
    const decades = ref<GenreDO[]>([]);
    const selectedGenreId = ref<number | null>(null);

    async function fetchGenres() {
        try {
            const response = await fetch("https://127.0.0.1:8001/api/genres");

            if (!response.ok) {
                console.error("Failed to fetch genres");
                return;
            }

            for (const genre of (await response.json()) as GenreDO[]) {
                if (genre.genre_type === "genre") {
                    genres.value.push(genre);
                } else {
                    decades.value.push(genre);
                }
            }
        } catch (err) {
            console.error(err);
        }
    }

    return {
        gameState,
        prevScore,
        highScore,

        genres,
        decades,
        fetchGenres,
        selectedGenreId,
    };
});
