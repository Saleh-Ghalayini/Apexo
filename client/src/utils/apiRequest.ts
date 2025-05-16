import { AuthService } from '../services/authService';

// Function to check token validity and refresh if needed
export const checkAndRefreshToken = async (): Promise<boolean> => {
  const token = localStorage.getItem('auth_token');
  
  // If no token exists, user is not authenticated
  if (!token) {
    return false;
  }
  
  // Check token expiration
  const tokenData = JSON.parse(atob(token.split('.')[1])); // Decode JWT payload
  const expiration = tokenData.exp * 1000; // Convert to milliseconds
  const now = Date.now();
  
  // If token is expired or about to expire within next minute, refresh it
  if (expiration < now || expiration - now < 60000) {
    try {
      await AuthService.refreshToken();
      return true;
    } catch (error) {
      console.error('Error refreshing token:', error);
      // Token refresh failed, log user out
      AuthService.logout();
      return false;
    }
  }
  
  return true; // Token is valid
};

// Function to redirect unauthenticated users
export const redirectIfNotAuthenticated = (navigate: any): boolean => {
  if (!AuthService.isAuthenticated()) {
    navigate('/login');
    return false;
  }
  return true;
};