import React from 'react';
import Loading from '../Loading';
import { useAuth } from '../../hooks/useAuth';

const AuthRedirect: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { loading } = useAuth();

  if (loading) {
    return <Loading message="Checking authentication..." />;
  }

  return <>{children}</>;
};

export default AuthRedirect;