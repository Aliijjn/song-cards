type Quote = {
    score: number;
    quote: string;
    song: string;
    artist: string;
    year: string;
}

const quotes: Quote[] = [
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

export function getQuote(score: number) {
    const highest = quotes.reverse().find((q) => q.score <= score)

    if (!highest) {
        return {
            quote: "Error loading cheesy quote",
            info: ":(",
        }
    }

    return {
        quote: `<i>"${highest.quote}"</i>`,
        info: `- ${highest.artist}, <i>${highest.song} (${highest.year})</i>`,
    }
}