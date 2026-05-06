import apiRequest, { type ApiResult } from '@/tools/apiRequest.ts';
import CurationCreationDTO = App.Data.CurationCreationDTO;
import CurationDTO = App.Data.CurationDTO;
import CurationUpdateDTO = App.Data.CurationUpdateDTO;
import CurationCopyDTO = App.Data.CurationCopyDTO;
import SongEditCreationDTO = App.Data.SongEditCreationDTO;
import CurationCombineDTO = App.Data.CurationCombineDTO;

export default class CurationAPI {
  static getMultiple(start?: number, length?: number): Promise<ApiResult<CurationDTO[]>> {
    return apiRequest('/curations', { method: 'GET', query: { start, length } });
  }

  static get(id: string): Promise<ApiResult<CurationDTO>> {
    return apiRequest(`/curations/${id}`);
  }

  static update(id: string, update: CurationUpdateDTO): Promise<ApiResult<void>> {
    return apiRequest(`/curations/${id}`, { method: 'PUT', body: { update } });
  }

  static create(curation: CurationCreationDTO): Promise<ApiResult<string>> {
    return apiRequest('/curations', { method: 'POST', body: { curation } });
  }

  static copy(id: string, copy: CurationCopyDTO): Promise<ApiResult<string>> {
    return apiRequest(`/curations/${id}/copy`, { method: 'POST', body: { copy } });
  }

  static combine(id: string, combine: CurationCombineDTO): Promise<ApiResult<string>> {
    return apiRequest(`/curations/${id}/combine`, { method: 'POST', body: { combine } });
  }

  static toExport(id: string): Promise<ApiResult<string>> {
    return apiRequest(`/curations/${id}/export`);
  }

  static delete(id: string): Promise<ApiResult<void>> {
    return apiRequest(`/curations/${id}`, { method: 'DELETE' });
  }

  static deleteSong(curationId: string, songId: string): Promise<ApiResult<void>> {
    return apiRequest(`/curations/${curationId}/${songId}`, { method: 'DELETE' });
  }

  static putSongEdit(curationId: string, songEdit: SongEditCreationDTO): Promise<ApiResult<void>> {
    return apiRequest(`/curations/${curationId}/edit`, { method: 'PUT', body: { songEdit } });
  }
}
