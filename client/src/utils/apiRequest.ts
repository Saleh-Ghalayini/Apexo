import api from '../services/api';
import type { AxiosRequestConfig } from 'axios';

// Define the RequestConfig type using the imported AxiosRequestConfig
type RequestConfig = AxiosRequestConfig | {
  url: string;
  method: string;
  data?: unknown;
  params?: Record<string, unknown>;
  headers?: Record<string, string>;
}

/**
 * Wrapper function for API requests that handles standard response format
 * with success flag and payload
 */
export async function apiRequest<T = unknown>(config: RequestConfig): Promise<T> {
  try {
    const response = await api.request<T>(config);
    return response.data as T;
  } catch (error: unknown) {
    throw error;
  }
}