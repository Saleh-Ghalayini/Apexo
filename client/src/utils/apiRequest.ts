import { AuthService } from '../services/authService';

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