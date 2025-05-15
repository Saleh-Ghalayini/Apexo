import { useEffect, useState } from 'react';
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

  return (
    <>
      <button
        type="button"
        className="modal-button primary"
        disabled={!userId || loading}
      >
        {loading ? 'Connecting...' : 'Connect Notion'}
      </button>
      {error && (
        <div style={{ color: 'red', marginTop: '8px', fontSize: '14px' }}>
          {error}
        </div>
      )}
    </>
  );
}