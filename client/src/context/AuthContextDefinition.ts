import { createContext } from 'react';
import type { User, RegisterRequest } from '../services/authService';

export interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  login: (email: string, password: string) => Promise<unknown>;
  register: (userData: RegisterRequest) => Promise<unknown>;
  logout: () => Promise<void>;
  loading: boolean;
  refreshToken: () => Promise<void>;
}

export const AuthContext = createContext<AuthContextType | undefined>(undefined);