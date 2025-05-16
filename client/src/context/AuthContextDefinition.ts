import { createContext } from 'react';

export interface AuthContextType {
  user: any;
  isAuthenticated: boolean;
  login: (email: string, password: string) => Promise<unknown>;
  register: (userData: any) => Promise<unknown>;
  logout: () => Promise<void>;
  loading: boolean;
  refreshToken: () => Promise<void>;
}

export const AuthContext = createContext<AuthContextType | undefined>(undefined);