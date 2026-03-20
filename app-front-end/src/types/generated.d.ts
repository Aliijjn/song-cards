declare namespace App.Data {
export type ExportDO = {
uuid: string;
user_id: number;
name: string;
created_at: string;
updated_at: string;
};
export type GenreDTO = {
id: number;
name: string;
description: string | null;
song_count: number;
showcased_album: string;
};
export type PlaylistDTO = {
id: string;
name: string;
ownerName: string | null;
imageUrl: string | null;
songCount: number;
};
export type PlaylistResultDTO = {
playlists: Array<any>;
isLast: boolean;
};
export type SongCardDTO = {
id: string;
name: string;
artist: string;
release_year: number;
url: string;
color: string;
errors: Array<any>;
imageUrl: string;
};
export type SongDTO = {
name: string;
artist_name: Array<any>;
album_name: string;
album_cover_url: string;
duration_seconds: number;
release_date: string;
};
}
declare namespace App.Enum {
export type GenreTypeEnum = 'genre' | 'decade';
export type SongErrorEnum = 1 | 2;
}
