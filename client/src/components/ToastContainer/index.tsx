import React, { useState } from 'react';
import Toast from '../Toast';
import './ToastContainer.css';

interface ToastMessage {
  id: string;
  message: string;
  type: 'success' | 'error' | 'info';
}

const ToastContainer: React.FC = () => {
  const [toasts, setToasts] = useState<ToastMessage[]>([]);

  React.useEffect(() => {
    const showToast = (message: string, type: 'success' | 'error' | 'info') => {
      const id = Math.random().toString(36).substring(2, 9);
      setToasts(prevToasts => [...prevToasts, { id, message, type }]);
    };

    // Expose the method globally
    (window as any).showToast = showToast;

    return () => {
      delete (window as any).showToast;
    };
  }, []);

  const removeToast = (id: string) => {
    setToasts(toasts.filter(toast => toast.id !== id));
  };

  return (
    <div className="toast-container">
      {toasts.map(toast => (
        <Toast
          key={toast.id}
          message={toast.message}
          type={toast.type}
          onClose={() => removeToast(toast.id)}
        />
      ))}
    </div>
  );
};

export default ToastContainer;
