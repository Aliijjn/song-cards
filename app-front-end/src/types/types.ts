export type SongData = {
  title:                  string;
  artist:                 string;
  album:                  string;
  song_duration:          number;
  release_date:           number;
  total_views_on_spotify: number;
}

export type GuessableStatKey = 'song_duration' | 'release_date' | 'total_views_on_spotify';

export const guessableStatKeys: GuessableStatKey[] = [
  'song_duration',
  'release_date',
  'total_views_on_spotify',
];

type buttonState = 'default' | 'correct' | 'incorrect'

export type ButtonData = {
  song:     SongData;
  songStat: GuessableStatKey;
  index:    number;
  state:    buttonState;
}

export type GameData = {
  score:          number;
  selectedButton: number | null;
}

export type GameState = "start" | "playing" | "lost";

export type SessionData = {
  state:     GameState;
  prevScore: number;
  highScore: number;
}
