declare namespace App.Data {
export type CurationCombineDTO = {
name: string;
description: string | null;
userId: number;
keepOriginal: boolean;
curationIds: Array<any>;
};
export type CurationCopyDTO = {
name: string;
description: string | null;
userId: number;
maxSongCount: number | null;
};
export type CurationCreationDTO = {
name: string;
description: string | null;
userId: number;
playlistIds: Array<any>;
};
export type CurationDTO = {
id: string;
name: string;
description: string | null;
createdBy: string;
updatedAt: string;
songs: Array<any>;
};
export type CurationUpdateDTO = {
name: string;
description: string | null;
};
export type ExportDTO = {
id: string;
user_id: number;
user_name: string;
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
id: string;
spotifyId: string;
name: string;
artist_name: Array<any>;
album_name: string;
albumCoverUrl: string | null;
duration_seconds: number;
release_date: string;
errors: Array<any>;
};
export type SongEditCreationDTO = {
song_id: string;
name: string;
release_date: string;
};
}
declare namespace App.Enum {
export type GenreTypeEnum = 'genre' | 'decade';
export type SongErrorEnum = 1 | 2;
}
