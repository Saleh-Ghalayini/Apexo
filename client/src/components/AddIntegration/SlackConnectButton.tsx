import { useEffect, useState, useCallback } from 'react';
import api from '../../services/api';

const NGROK_BASE = 'https://e5b9-93-126-210-243.ngrok-free.app';
const SLACK_REDIRECT_PATH = '/api/v1/integrations/slack/redirect';

interface SlackConnectButtonProps {
  onSuccess?: () => void;
}

export default function SlackConnectButton({ onSuccess }: SlackConnectButtonProps) {
  const [userId, setUserId] = useState<string | null>(null);

  useEffect(() => {
    api.get('/user')
      .then(res => {
        const id = res.data?.payload?.id || res.data?.id;
        if (id) setUserId(id.toString());
      })
      .catch(() => setUserId(null));
  }, []);

  const handleStorage = useCallback((event: StorageEvent) => {
    if (event.key === 'slack_auth_completed' && event.newValue === 'true') {
      if (onSuccess) onSuccess();
      localStorage.removeItem('slack_auth_completed');
      window.removeEventListener('storage', handleStorage);
    }
  }, [onSuccess]);

  const handleClick = () => {
    localStorage.setItem('slack_auth_initiated', 'true');
    if (onSuccess) {
      window.addEventListener('storage', handleStorage);
    }
  };

  const slackUrl = userId
    ? `${NGROK_BASE}${SLACK_REDIRECT_PATH}?user_id=${userId}`
    : `${NGROK_BASE}${SLACK_REDIRECT_PATH}`;

  return (
    <a href={slackUrl} target="_blank" rel="noopener noreferrer" onClick={handleClick}>
      <button type="button" className="btn btn-slack" disabled={!userId}>
        Connect Slack
      </button>
    </a>
  );
}