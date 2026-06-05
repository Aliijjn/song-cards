import apiRequest, { type ApiResult } from '@/tools/apiRequest.ts';
import CurationCreationDTO = App.Data.CurationCreationDTO;
import CurationCollectionDTO = App.Data.CurationCollectionDTO;
import CurationDTO = App.Data.CurationDTO;
import CurationUpdateDTO = App.Data.CurationUpdateDTO;
import CurationCopyDTO = App.Data.CurationCopyDTO;
import SongEditCreationDTO = App.Data.SongEditCreationDTO;
import CurationCombineDTO = App.Data.CurationCombineDTO;
import CurationCreationFromSongsDTO = App.Data.CurationCreationFromSongsDTO;
import CurationSummaryDTO = App.Data.CurationSummaryDTO;

export default class CurationAPI {
  private static readonly prefix = '/curations';

  static all(): Promise<ApiResult<CurationSummaryDTO[]>> {
    return apiRequest(`${this.prefix}`, { method: 'GET' });
  }

  static allByType(): Promise<ApiResult<CurationCollectionDTO>> {
    return apiRequest(`${this.prefix}/by-type`, { method: 'GET' });
  }

  static get(id: string): Promise<ApiResult<CurationDTO>> {
    return apiRequest(`${this.prefix}/${id}`);
  }

  static update(id: string, update: CurationUpdateDTO): Promise<ApiResult<void>> {
    return apiRequest(`${this.prefix}/${id}`, { method: 'PUT', body: { update } });
  }

  static create(curation: CurationCreationDTO): Promise<ApiResult<string>> {
    return apiRequest(`${this.prefix}`, { method: 'POST', body: { curation } });
  }

  static createFromSongs(
    curationId: string,
    creationDto: CurationCreationFromSongsDTO
  ): Promise<ApiResult<string>> {
    return apiRequest(`${this.prefix}/${curationId}/from-songs`, {
      method: 'POST',
      body: { creationDto },
    });
  }

  static copy(id: string, copy: CurationCopyDTO): Promise<ApiResult<string>> {
    return apiRequest(`${this.prefix}/${id}/copy`, { method: 'POST', body: { copy } });
  }

  static combine(id: string, combine: CurationCombineDTO): Promise<ApiResult<string>> {
    return apiRequest(`${this.prefix}/${id}/combine`, { method: 'POST', body: { combine } });
  }

  static toExport(id: string, skipErrors?: boolean): Promise<ApiResult<string>> {
    return apiRequest(`${this.prefix}/${id}/export`, { query: { skip_errors: skipErrors } });
  }

  static delete(id: string): Promise<ApiResult<void>> {
    return apiRequest(`${this.prefix}/${id}`, { method: 'DELETE' });
  }

  static deleteSong(curationId: string, songId: string): Promise<ApiResult<void>> {
    return apiRequest(`${this.prefix}/${curationId}/song/${songId}`, { method: 'DELETE' });
  }

  static deleteSongs(curationId: string, songIds: string[]): Promise<ApiResult<void>> {
    return apiRequest(`${this.prefix}/${curationId}/songs`, {
      method: 'DELETE',
      body: { songIds },
    });
  }

  static putSongEdit(curationId: string, songEdit: SongEditCreationDTO): Promise<ApiResult<void>> {
    return apiRequest(`${this.prefix}/${curationId}/edit`, {
      method: 'PUT',
      body: { songEdit },
    });
  }
}
