import api from '../services/api';
import type { AxiosRequestConfig } from 'axios';

/**
 * RequestConfig can be a standard AxiosRequestConfig or a simplified config object.
 */
type RequestConfig = AxiosRequestConfig | {
  url: string;
  method: string;
  data?: unknown;
  params?: Record<string, unknown>;
  headers?: Record<string, string>;
};

/**
 * Wrapper function for API requests that handles standard response format
 * with success flag and payload. Throws a normalized error object on failure.
 */
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