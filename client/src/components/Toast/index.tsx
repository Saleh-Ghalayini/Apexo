import React from 'react';

interface ToastProps {
  message: string;
  type: 'success' | 'error' | 'info';
  onClose: () => void;
}

const Toast: React.FC<ToastProps> = ({ message, type, onClose }) => (
  <div className={`toast ${type}`}>
    <span>{message}</span>
    <button onClick={onClose}>×</button>
  </div>
);

export default Toast;