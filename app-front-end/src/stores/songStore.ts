import { defineStore } from "pinia";
import { useBaseStore } from "@/stores/baseStore.ts";
import { ref, computed } from "vue";
import dayjs from "dayjs";
import { type Comparison, type SongDTO } from "@/src/types/types";

export const useSongStore = defineStore("songStore", () => {
    const songs = ref<SongDTO[]>([]);
    const comparisons: Comparison[] = [
        {
            type: "duration_seconds",
            name: "duration",
            description: "Which song is <b>longer</b>?",
            compareFn: (a, b) => b - a,
            formatFn: (val: number) => `${Math.floor(val / 60)}:${(val % 60).toString().padStart(2, "0")}`,
        },
        {
            type: "release_date",
            name: "release date",
            description: "Which song is <b>newer</b>?",
            compareFn: (a, b) => b.localeCompare(a),
            formatFn: (val: string) => dayjs(val).format("MMM D, YYYY"),
        }
    ]

    const songIndex = ref(0);
    const comparisonIndex = ref(0);

    async function getSongs() {
        const store = useBaseStore();

        songIndex.value = 0;
        const args = `?genre_id=${store.selectedGenre?.id?.toString() ?? ""}&difficulty=${store.difficulty?.value}`;
        const response = await fetch(
            `https://127.0.0.1:8001/api/songs${args}`
        )
        if (response.ok) {
            songs.value = await response.json()
        } else {
            console.error("Failed to fetch songs")
        }
    }

    const currentSongs = computed((): null | SongDTO[] => {
        if (songs.value.length < 2) {
            return null
        }

        return [
            songs.value[songIndex.value % songs.value.length]!,
            songs.value[(songIndex.value + 1) % songs.value.length]!,
        ]
    })
    const currentComparison = computed((): Comparison => {
        return comparisons[comparisonIndex.value % comparisons.length]!;
    })

    function isCorrectCard(index: number): boolean {
        if (!currentSongs.value) return false;

        const comparisonType = currentComparison.value.type;
        const result = currentComparison.value
            .compareFn(currentSongs.value[0][comparisonType]!, currentSongs.value[1][comparisonType]!);
        console.log(index, currentSongs.value[0][comparisonType], currentSongs.value[1][comparisonType], result);

        return result === 0 || (result < 0 && index === 0) || (result > 0 && index === 1);
    }

    function increment() {
        songIndex.value++;
        comparisonIndex.value++;
    }

    return {
        songs,
        getSongs,
        currentSongs,
        currentComparison,
        isCorrectCard,
        increment,
    }
});