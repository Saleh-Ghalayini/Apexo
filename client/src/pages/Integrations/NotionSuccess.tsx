import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import './NotionSuccess.css';

const NotionSuccess: React.FC = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading');
  const [message, setMessage] = useState('');

  useEffect(() => {
    setStatus('loading');
    setMessage('Processing...');
  }, [location.search, navigate]);

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