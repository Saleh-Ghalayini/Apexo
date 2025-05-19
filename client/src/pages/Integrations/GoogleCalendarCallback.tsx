import React, { useEffect, useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';

const GoogleCalendarCallback: React.FC = () => {
  const [status, setStatus] = useState<'pending' | 'success' | 'error'>('pending');
  const [message, setMessage] = useState('Connecting your Google Calendar...');
  const [details, setDetails] = useState<string | null>(null);
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();

  useEffect(() => {
    const code = searchParams.get('code');
    const error = searchParams.get('error');
    const token = localStorage.getItem('access_token');

    if (error) {
      setStatus('error');
      setMessage('Authorization failed.');
      setDetails(error);
      return;
    }

    if (code && token) {
      fetch('http://localhost:8000/api/v1/google-calendar/save-token', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify({ code }),
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            setStatus('success');
            setMessage(data.payload?.message || 'Google Calendar connected!');
            setTimeout(() => navigate('/integrations'), 2000);
          } else {
            setStatus('error');
            setMessage('Failed to connect Google Calendar.');
            setDetails(data.error || null);
          }
        })
        .catch(() => {
          setStatus('error');
          setMessage('Failed to connect Google Calendar. Please try again.');
        });
    } else {
      setStatus('error');
      setMessage('Missing code or user authentication.');
    }
  }, [navigate, searchParams]);

  return (
    <div style={{ fontFamily: 'sans-serif', textAlign: 'center', paddingTop: 60 }}>
      <h2>{status === 'success' ? 'Success!' : status === 'error' ? 'Error' : 'Connecting...'}</h2>
      <p>{message}</p>
      {details && <pre style={{ color: '#b00', marginTop: 16 }}>{details}</pre>}
    </div>
  );
};

export default GoogleCalendarCallback;
