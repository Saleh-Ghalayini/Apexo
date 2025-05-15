import React from 'react';
import './Loading.css';

interface LoadingProps {
  message?: string;
}

const Loading: React.FC<LoadingProps> = ({ message = 'Loading...' }) => {
  return (
    <div className="loading-screen">
      <div className="simple-spinner" />
      <div className="loading-text">{message}</div>
    </div>
  );
};

export default Loading;
