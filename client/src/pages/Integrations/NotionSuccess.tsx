import React, { useEffect, useState } from 'react';

const NotionSuccess: React.FC = () => {
  const [status, setStatus] = useState<'loading' | 'success' | 'error'>('loading');
  const [message, setMessage] = useState('');

  useEffect(() => {
    // Placeholder for status logic
    setStatus('loading');
    setMessage('Processing...');
  }, []);

  return (
    <div>
      <h2>{status === 'loading' ? 'Loading...' : status === 'success' ? 'Success!' : 'Error'}</h2>
      <p>{message}</p>
    </div>
  );
};

export default NotionSuccess;