import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import './NotionSuccess.css';

const NotionSuccess: React.FC = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading');
  const [message, setMessage] = useState('');
  useEffect(() => {
    // Parse the query parameters 
    const params = new URLSearchParams(location.search);
    const statusParam = params.get('status');
    const errorMessage = params.get('message');
    if (statusParam === 'success') {
      setStatus('success');
      setMessage('Successfully connected to Notion!');
      
      // Signal to the parent window that the auth was successful
      localStorage.removeItem('notion_auth_completed');  // Remove first to ensure event fires even if value doesn't change
      localStorage.setItem('notion_auth_completed', 'true');
      
      // Try to notify the opener window directly if available
      if (window.opener && !window.opener.closed) {
        try {
          window.opener.postMessage('notion_auth_completed', '*');
        } catch (e) {
          console.error('Could not send message to opener:', e);
        }
      }
      
      // Redirect after a delay
      setTimeout(() => {
        navigate('/notion/databases');
      }, 3000);
    } else {
      setStatus('error');
      setMessage(errorMessage || 'Failed to connect to Notion. Please try again.');
      
      // Redirect after a delay
      setTimeout(() => {
        navigate('/integrations');
      }, 5000);
    }
  }, [location.search, navigate]);

  return (
    <div className="notion-success-container">
      <div className="notion-success-card">
        {status === 'loading' && (
          <div className="notion-success-content loading">
            <div className="spinner"></div>
            <h2>Processing Notion Connection...</h2>
          </div>
        )}

        {status === 'success' && (
          <div className="notion-success-content success">
            <div className="status-icon success">✓</div>
            <h2>Success!</h2>
            <p>{message}</p>
            <p className="redirect-message">Redirecting to Notion Databases...</p>
          </div>
        )}

        {status === 'error' && (
          <div className="notion-success-content error">
            <div className="status-icon error">✗</div>
            <h2>Connection Error</h2>
            <p>{message}</p>
            <p className="redirect-message">Redirecting to Integrations page...</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default NotionSuccess;