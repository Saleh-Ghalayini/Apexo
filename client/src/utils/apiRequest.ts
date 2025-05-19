import api from '../services/api';
import type { AxiosRequestConfig } from 'axios';
import { AuthService } from '../services/authService';

export type RequestConfig = AxiosRequestConfig | {
  url: string;
  method: string;
  data?: unknown;
  params?: Record<string, unknown>;
  headers?: Record<string, string>;
};


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

export const getTokenExpiration = (token: string): number | null => {
  try {
    const tokenData = JSON.parse(atob(token.split('.')[1]));
    return typeof tokenData.exp === 'number' ? tokenData.exp * 1000 : null;
  } catch (e) {
    console.error('Invalid token format:', e);
    return null;
  }
};

export const checkAndRefreshToken = async (): Promise<boolean> => {
  const token = localStorage.getItem('auth_token');
  if (!token) {
    return false;
  }
  const expiration = getTokenExpiration(token);
  if (!expiration) {
    AuthService.logout();
    return false;
  }
  const now = Date.now();
  if (expiration < now || expiration - now < 60000) {
    try {
      await AuthService.refreshToken();
      return true;
    } catch (error) {
      console.error('Error refreshing token:', error);
      AuthService.logout();
      return false;
    }
  }
  return true;
};

export const redirectIfNotAuthenticated = (navigate: (path: string) => void): boolean => {
  if (!AuthService.isAuthenticated()) {
    navigate('/login');
    return false;
  }
  return true;
};


export async function autoDownloadFile(url: string, filename?: string) {
  const token = localStorage.getItem('auth_token');
  const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1';
 
  const fullUrl = url.startsWith('http') ? url : apiBase.replace(/\/$/, '') + (url.startsWith('/') ? url : '/' + url);
  const res = await fetch(fullUrl, {
    headers: token ? { 'Authorization': `Bearer ${token}` } : {},
  });
  if (!res.ok) throw new Error('Failed to download file');
  const blob = await res.blob();
  const contentDisposition = res.headers.get('Content-Disposition');
  let suggestedName = filename;
  if (!suggestedName && contentDisposition) {
    const match = contentDisposition.match(/filename="?([^";]+)"?/);
    if (match) suggestedName = match[1];
  }
  if (!suggestedName) suggestedName = 'report_' + Date.now();
  const link = document.createElement('a');
  link.href = window.URL.createObjectURL(blob);
  link.download = suggestedName;
  document.body.appendChild(link);
  link.click();
  setTimeout(() => {
    window.URL.revokeObjectURL(link.href);
    link.remove();
  }, 1000);
}