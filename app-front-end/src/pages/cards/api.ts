import apiRequest, { type ApiResult } from "@/tools/apiRequest.ts";
import type {PlaylistResultDTO, SongCardDTO} from "@/types/types.ts";
import ExportDO = App.Data.ExportDO;

export function getPlaylists(): Promise<ApiResult<PlaylistResultDTO>> {
    return apiRequest("/playlists");
}

export function getCardData(playlist_ids: string[]): Promise<ApiResult<{ valid: SongCardDTO[]; invalid: SongCardDTO[] }>> {
    return apiRequest("/export/data", { method: 'POST', body: { playlist_ids } })
}

export function exportCardData(card_data: SongCardDTO[]): Promise<ApiResult<{ preview: string; download: string }>> {
    return apiRequest("/export", { method: 'POST', body: { card_data } })
}

export function fetchExports(user_id: number = 1): Promise<ApiResult<ExportDO[]>> {
    return apiRequest("/export", { query: { user_id } });
}