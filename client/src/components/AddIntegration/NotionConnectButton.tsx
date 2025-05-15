import { useEffect, useState, useCallback } from 'react';
import api from '../../services/api';

interface NotionConnectButtonProps {
  onSuccess?: () => void;
}

export default function NotionConnectButton({ onSuccess }: NotionConnectButtonProps) {
  const [userId, setUserId] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api.get('/user')
      .then(res => {
        const id = res.data?.payload?.id || res.data?.id;
        if (id) setUserId(id.toString());
      })
      .catch(() => setUserId(null));
  }, []);

  // Memoize handlers to avoid unnecessary re-registrations
  const handleMessageEvent = useCallback((event: MessageEvent) => {
    if (event.data === 'notion_auth_completed') {
      if (onSuccess) onSuccess();
      window.removeEventListener('message', handleMessageEvent);
      window.removeEventListener('storage', handleStorageChange);
    }
  }, [onSuccess]);

  const handleStorageChange = useCallback((event: StorageEvent) => {
    if (event.key === 'notion_auth_completed' && event.newValue === 'true') {
      if (onSuccess) onSuccess();
      localStorage.removeItem('notion_auth_completed');
      window.removeEventListener('storage', handleStorageChange);
    }
  }, [onSuccess]);

  useEffect(() => {
    return () => {
      window.removeEventListener('message', handleMessageEvent);
      window.removeEventListener('storage', handleStorageChange);
    };
  }, [handleMessageEvent, handleStorageChange]);

  const handleClick = async () => {
    try {
      setLoading(true);
      setError(null);
      localStorage.setItem('notion_auth_initiated', 'true');
      const response = await api.post(`/integrations/connect/notion`);
      if (response?.data?.success && response?.data?.payload?.url) {
        let redirectUrl = response.data.payload.url;
        if (redirectUrl.startsWith('/')) {
          redirectUrl = `http://localhost:8000${redirectUrl}`;
        }
        if (userId) {
          const separator = redirectUrl.includes('?') ? '&' : '?';
          redirectUrl += `${separator}user_id=${userId}`;
        }
        const popupWindow = window.open(redirectUrl, '_blank');
        window.addEventListener('storage', handleStorageChange);
        window.addEventListener('message', handleMessageEvent);
        if (!popupWindow || popupWindow.closed || typeof popupWindow.closed === 'undefined') {
          setError('Popup was blocked. Please allow popups for this site.');
          setLoading(false);
        }
      } else {
        throw new Error('Failed to get Notion redirect URL');
      }
    } catch (err) {
      setError('Failed to connect to Notion. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <>
      <button
        type="button"
        className="modal-button primary"
        onClick={handleClick}
        disabled={!userId || loading}
        style={{
          backgroundColor: '#000000',
          color: '#ffffff',
          display: 'inline-flex',
          alignItems: 'center',
          justifyContent: 'center',
          gap: '8px',
          padding: '8px 16px',
          borderRadius: '4px',
          fontWeight: 500,
          cursor: !userId || loading ? 'not-allowed' : 'pointer',
          opacity: !userId || loading ? 0.7 : 1
        }}
      >
        {loading ? (
          <span>Connecting...</span>
        ) : (
          <>
            <span style={{ fontSize: '16px' }}>N</span>
            <span>Connect Notion</span>
          </>
        )}
      </button>
      {error && (
        <div style={{ color: 'red', marginTop: '8px', fontSize: '14px' }}>
          {error}
        </div>
      )}
    </>
  );
}