export type SongDTO = {
  name:             string;
  artist_name:      string[];
  album_name:       string;
  album_cover_url:  string;
  duration_seconds: number;
  release_date:     string;
}

export const guessableStatKeys = [
  'duration_seconds',
  'release_date',
] as const;

export type GuessableStatKey = typeof guessableStatKeys[number];

type buttonState = 'default' | 'correct' | 'incorrect'

export type ButtonData = {
  song:     SongDTO;
  songStat: GuessableStatKey;
  index:    number;
  state:    buttonState;
}

export type GameData = {
  score:          number;
  selectedButton: number | null;
}

export type GameState = "start" | "playing" | "lost";

export type GenreDTO = {
  id: number;
  name: string;
  description: string;
  song_count: number;
  // description: string;
  showcased_album: string;
  // genre_type: "genre" | "decade";
}
