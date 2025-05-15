import { useState } from 'react';

interface NotionConnectButtonProps {
  onSuccess?: () => void;
}

export default function NotionConnectButton({ onSuccess }: NotionConnectButtonProps) {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  return (
    <>
      <button
        type="button"
        className="modal-button primary"
        disabled={loading}
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