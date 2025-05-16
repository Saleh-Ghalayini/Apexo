import api from './api';

// Types for authentication requests and responses

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
  job_title?: string;
  department?: string;
  phone?: string;
  avatar?: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  job_title?: string;
  department?: string;
  phone?: string;
  avatar?: string;
  company_id: number;
  created_at: string;
  updated_at: string;
}

export interface Company {
  id: number;
  name: string;
  domain: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface AuthResponse {
  success: boolean;
  payload: {
    message: string;
    user: User;
    token: string;
    token_type: string;
    expires_in: number;
    company?: Company;
  };
}

// AuthService provides authentication-related API calls and localStorage management
export const AuthService = {
  async login(credentials: LoginRequest): Promise<AuthResponse> {
    try {
      const response = await api.post<AuthResponse>('/auth/login', credentials);
      if (response.data.payload.token) {
        localStorage.setItem('auth_token', response.data.payload.token);
        localStorage.setItem('user_data', JSON.stringify(response.data.payload.user));
      }
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  async register(userData: RegisterRequest): Promise<AuthResponse> {
    try {
      const response = await api.post<AuthResponse>('/auth/register', userData);
      if (response.data.payload.token) {
        localStorage.setItem('auth_token', response.data.payload.token);
        localStorage.setItem('user_data', JSON.stringify(response.data.payload.user));
      }
      return response.data;
    } catch (error) {
      throw error;
    }
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

  getCurrentUser(): User | null {
    const userData = localStorage.getItem('user_data');
    if (userData) {
      try {
        return JSON.parse(userData) as User;
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