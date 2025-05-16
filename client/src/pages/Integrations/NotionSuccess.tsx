import React, { useEffect, useState } from 'react';
import './NotionSuccess.css';

const NotionSuccess: React.FC = () => {
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading');
  const [message, setMessage] = useState('');

  useEffect(() => {
    setStatus('loading');
    setMessage('Processing...');
  }, []);

  return (
    <div className="notion-success-container">
      <div className="notion-success-card">
        <h2>{status === 'loading' ? 'Loading...' : status === 'success' ? 'Success!' : 'Error'}</h2>
        <p>{message}</p>
      </div>
    </div>
  );
};

export default NotionSuccess;