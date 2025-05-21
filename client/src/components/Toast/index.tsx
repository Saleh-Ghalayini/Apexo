import React, { useState, useEffect } from 'react';
import './Toast.css';

interface ToastProps {
  message: string;
  type: 'success' | 'error' | 'info';
  duration?: number;
  onClose: () => void;
  autoClose?: boolean;
  autoCloseTime?: number;
}

const Toast: React.FC<ToastProps> = ({ message, type, duration = 3000, onClose, autoClose = true, autoCloseTime }) => {
  const [visible, setVisible] = useState(true);

  useEffect(() => {
    if (!autoClose) return;
    const timer = setTimeout(() => {
      setVisible(false);
      setTimeout(() => {
        onClose();
      }, 300);
    }, autoCloseTime || duration);

    return () => clearTimeout(timer);
  }, [duration, onClose, autoClose, autoCloseTime]);

  return (
    <div className={`toast ${type} ${visible ? 'visible' : 'hidden'}`}>
      <div className="toast-content">
        {type === 'success' && <span className="toast-icon">✓</span>}
        {type === 'error' && <span className="toast-icon">✕</span>}
        {type === 'info' && <span className="toast-icon">ℹ</span>}
        <span className="toast-message">{message}</span>
      </div>
      <button className="toast-close" onClick={() => setVisible(false)}>×</button>
    </div>
  );
};

export default Toast;