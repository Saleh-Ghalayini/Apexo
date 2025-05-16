import api from '../services/api';
import type { AxiosRequestConfig } from 'axios';

type RequestConfig = AxiosRequestConfig | {
  url: string;
  method: string;
  data?: unknown;
  params?: Record<string, unknown>;
  headers?: Record<string, string>;
}

export async function apiRequest<T = unknown>(config: RequestConfig): Promise<T> {
  try {
    const response = await api.request<T>(config);
    return response.data as T;
  } catch (error: unknown) {
    console.error('API Request Failed:', error);

    if (
      typeof error === 'object' && 
      error !== null && 
      'response' in error && 
      error.response && 
      typeof error.response === 'object' && 
      'data' in error.response
    ) {
      throw error.response.data;
    }

    const err = error as Error;
    throw {
      success: false,
      message: err.message || 'An unknown error occurred',
      error: err
    };
  }
}