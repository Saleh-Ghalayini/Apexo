import React, { useState } from 'react';
import type { ReactNode } from 'react';
import { AuthService } from '../services/authService';
import type { User, RegisterRequest } from '../services/authService';
import { AuthContext } from './AuthContextDefinition';

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);

  const login = async (email: string, password: string) => {
    const response = await AuthService.login({ email, password });
    setUser(response.payload.user);
    return response;
  };

  const register = async (userData: RegisterRequest) => {
    const response = await AuthService.register(userData);
    setUser(response.payload.user);
    return response;
  };

  const logout = async () => {
    await AuthService.logout();
    setUser(null);
  };

  const value = {
    user,
    isAuthenticated: !!user,
    login,
    register,
    logout,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};