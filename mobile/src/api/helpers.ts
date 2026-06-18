import { ApiResponse } from './types';

export function getData(response: ApiResponse<any>): any {
  return response.data;
}
