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
    return response.data;
  },

  async register(userData: RegisterRequest): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/auth/register', userData);
    return response.data;
  },
};