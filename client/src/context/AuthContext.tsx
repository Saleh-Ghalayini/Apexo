import React, { useState } from 'react';
import type { ReactNode } from 'react';
import { AuthContext } from './AuthContextDefinition';

interface AuthProviderProps {
  children: ReactNode;
}

export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
  const [user, setUser] = useState(null);

  const value = {
    user,
    isAuthenticated: !!user,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};