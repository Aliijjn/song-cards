export const guessableStatKeys = ['duration_seconds', 'release_date'] as const;

export type GuessableStatKey = (typeof guessableStatKeys)[number];

export type SongCardState = 'idle' | 'correct' | 'incorrect';

export type ButtonData = {
  song: SongDTO;
  songStat: GuessableStatKey;
  index: number;
  state: SongCardState;
};

export type GameData = {
  score: number;
  selectedButton: number | null;
};

export type GenreDTO = {
  id: number;
  name: string;
  description: string;
  song_count: number;
  // description: string;
  showcased_album: string;
  // genre_type: "genre" | "decade";
};

export type DifficultyName = 'Easy' | 'Medium' | 'Hard' | 'Custom';

export type Difficulty = {
  name: DifficultyName;
  value: number;
  colour: string;
};

export const difficulties: Record<DifficultyName, Difficulty> = {
  Easy: { name: 'Easy', value: 75, colour: '#689f38' },
  Medium: { name: 'Medium', value: 60, colour: '#ffa000' },
  Hard: { name: 'Hard', value: 40, colour: '#e53935' },
  Custom: { name: 'Custom', value: 50, colour: '#818cf8' },
};

export type Comparison = {
  type: keyof SongDTO;
  name: string;
  description: string;
  compareFn: (a: any, b: any) => number;
  formatFn: (val: any) => string;
};
export type PlaylistDTO = {
  id: string;
  name: string;
  ownerName: string | null;
  imageUrl: string | null;
  songCount: number;
};

export type PlaylistResultDTO = {
  playlists: PlaylistDTO[];
  isLast: boolean;
};

export type SongCardDTO = {
  id: string;
  name: string;
  artist: string;
  release_year: number;
  url: string;
  color: string;
  errors: number[];
  imageUrl: string;
};
