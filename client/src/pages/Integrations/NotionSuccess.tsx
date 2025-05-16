import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import './NotionSuccess.css';

const NotionSuccess: React.FC = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading');
  const [message, setMessage] = useState('');

  useEffect(() => {
    const params = new URLSearchParams(location.search);
    const statusParam = params.get('status');
    const errorMessage = params.get('message');
    if (statusParam === 'success') {
      setStatus('success');
      setMessage('Successfully connected to Notion!');
      localStorage.removeItem('notion_auth_completed');
      localStorage.setItem('notion_auth_completed', 'true');
      if (window.opener && !window.opener.closed) {
        try {
          window.opener.postMessage('notion_auth_completed', '*');
        } catch (e) {
          console.error('Could not send message to opener:', e);
        }
      }
      setTimeout(() => {
        navigate('/notion/databases');
      }, 3000);
    } else {
      setStatus('error');
      setMessage(errorMessage || 'Failed to connect to Notion. Please try again.');
      setTimeout(() => {
        navigate('/integrations');
      }, 5000);
    }
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