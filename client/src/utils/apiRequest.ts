import api from '../services/api';
import type { AxiosRequestConfig } from 'axios';
import { AuthService } from '../services/authService';

// Define the RequestConfig type using the imported AxiosRequestConfig
export type RequestConfig = AxiosRequestConfig | {
  url: string;
  method: string;
  data?: unknown;
  params?: Record<string, unknown>;
  headers?: Record<string, string>;
};

/**
 * Wrapper function for API requests that handles standard response format
 * with success flag and payload
 */
export async function apiRequest<T = unknown>(config: RequestConfig): Promise<T> {
  try {
    const response = await api.request<T>(config);
    return response.data as T;
  } catch (error: unknown) {
    console.error('API Request Failed:', error);
    // Check if error is an Axios error with response
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
    // Otherwise throw a generic error
    const err = error as Error;
    throw {
      success: false,
      message: err.message || 'An unknown error occurred',
      error: err
    };
  }
}

/**
 * Gets the expiration timestamp from a JWT token.
 * @param token JWT token string.
 * @returns {number | null} Expiration timestamp in ms, or null if invalid.
 */
export const getTokenExpiration = (token: string): number | null => {
  try {
    const tokenData = JSON.parse(atob(token.split('.')[1]));
    return typeof tokenData.exp === 'number' ? tokenData.exp * 1000 : null;
  } catch (e) {
    console.error('Invalid token format:', e);
    return null;
  }
};

/**
 * Checks if the JWT token is valid and refreshes it if expired or about to expire.
 * @returns {Promise<boolean>} True if token is valid or refreshed, false otherwise.
 */
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

/**
 * Redirects to login if user is not authenticated.
 * @param navigate Navigation function (e.g., from react-router).
 * @returns {boolean} True if authenticated, false otherwise.
 */
export const redirectIfNotAuthenticated = (navigate: (path: string) => void): boolean => {
  if (!AuthService.isAuthenticated()) {
    navigate('/login');
    return false;
  }
  return true;
};