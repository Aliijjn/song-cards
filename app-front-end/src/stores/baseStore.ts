import { defineStore } from "pinia";
import { ref } from "vue";
import { difficulties, type Difficulty, type GameState, type GenreDTO } from "@/src/types/types.ts";

export const useBaseStore = defineStore("baseStore", () => {
    const gameState = ref<GameState>('start');

    const prevScore = ref<number>(0);
    const highScore = ref<number>(0);

    const genres = ref<GenreDTO[]>([]);
    // const decades = ref<GenreDTO[]>([]);
    const selectedGenre = ref<GenreDTO | null>(null);
    const startMatchModal = ref<boolean>(false);

    const difficulty = ref<Difficulty>(difficulties['Medium']);

    async function fetchGenres() {
        try {
            const response = await fetch("https://127.0.0.1:8001/api/genres");

            if (!response.ok) {
                console.error("Failed to fetch genres");
                return;
            }

            for (const genre of (await response.json()) as GenreDTO[]) {
                // if (genre.genre_type === "genre") {
                    genres.value.push(genre);
                // } else {
                //     decades.value.push(genre);
                // }
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
        // decades,
        fetchGenres,
        selectedGenre,
        startMatchModal,

        difficulty,
    };
});
