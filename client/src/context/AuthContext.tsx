import React, { useState, useEffect } from 'react';
import type { ReactNode } from 'react';
import { AuthService } from '../services/authService';
import type { User, RegisterRequest } from '../services/authService';
import { checkAndRefreshToken } from '../utils/auth';
import { AuthContext } from './AuthContextDefinition';

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    const checkAuth = async () => {
      try {
        await checkAndRefreshToken();
        const currentUser = AuthService.getCurrentUser();
        setUser(currentUser);
      } catch {
        setUser(null);
      } finally {
        setLoading(false);
      }
    };
    checkAuth();
  }, []);

  const login = async (email: string, password: string) => {
    setLoading(true);
    const response = await AuthService.login({ email, password });
    setUser(response.payload.user);
    setLoading(false);
    return response;
  };

  const register = async (userData: RegisterRequest) => {
    setLoading(true);
    const response = await AuthService.register(userData);
    setUser(response.payload.user);
    setLoading(false);
    return response;
  };

  const logout = async () => {
    setLoading(true);
    await AuthService.logout();
    setUser(null);
    setLoading(false);
  };

  const refreshToken = async (): Promise<void> => {
    setLoading(true);
    try {
      await AuthService.refreshToken();
      const currentUser = AuthService.getCurrentUser();
      setUser(currentUser);
    } catch {
      await logout();
    } finally {
      setLoading(false);
    }
  };

  const value = {
    user,
    isAuthenticated: !!user,
    login,
    register,
    logout,
    refreshToken,
    loading,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};