import api from './api';

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  company_name: string;
  company_domain: string;
  role: 'employee' | 'manager' | 'hr';
}

export interface AuthResponse {
  success: boolean;
  payload: {
    message: string;
    user: any;
    token: string;
    token_type: string;
    expires_in: number;
  };
}

export const AuthService = {
  async login(credentials: LoginRequest): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/login', credentials);
    if (response.data.payload.token) {
      localStorage.setItem('auth_token', response.data.payload.token);
      localStorage.setItem('user_data', JSON.stringify(response.data.payload.user));
    }
    return response.data;
  },

  async register(userData: RegisterRequest): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/register', userData);
    if (response.data.payload.token) {
      localStorage.setItem('auth_token', response.data.payload.token);
      localStorage.setItem('user_data', JSON.stringify(response.data.payload.user));
    }
    return response.data;
  },

  async logout(): Promise<void> {
    try {
      await api.post('/auth/logout');
    } catch (error) {
      console.error('Logout failed', error);
    } finally {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_data');
    }
  },

  getCurrentUser(): any | null {
    const userData = localStorage.getItem('user_data');
    if (userData) {
      try {
        return JSON.parse(userData);
      } catch (e) {
        console.error('Error parsing user data:', e);
        return null;
      }
    }
    return null;
  },

  async refreshToken(): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/refresh');
    if (response.data.payload.token) {
      localStorage.setItem('auth_token', response.data.payload.token);
    }
    return response.data;
  },

  isAuthenticated(): boolean {
    return !!localStorage.getItem('auth_token');
  },
};