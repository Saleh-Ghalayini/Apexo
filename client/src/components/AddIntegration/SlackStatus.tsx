import { useEffect, useState, useCallback } from 'react';
import api from '../../services/api';

interface Integration {
  provider: string;
  metadata?: {
    team?: {
      name?: string;
    };
  };
}

export default function SlackStatus() {
  const [connected, setConnected] = useState(false);
  const [workspace, setWorkspace] = useState<string | null>(null);

  const checkStatus = useCallback(() => {
    api.get('/user')
      .then(res => {
        const integrations = res.data?.payload?.integrations || [];
        const slack = integrations.find((i: Integration) => i.provider === 'slack');
        if (slack) {
          setConnected(true);
          setWorkspace(slack.metadata?.team?.name || null);
        } else {
          setConnected(false);
          setWorkspace(null);
        }
      })
      .catch(() => {
        setConnected(false);
        setWorkspace(null);
      });
  }, []);

  useEffect(() => {
    checkStatus();
    const intervalId = setInterval(() => {
      checkStatus();
    }, 5000);
    return () => clearInterval(intervalId);
  }, [checkStatus]);

  if (!connected) return <span style={{ color: 'red' }}>Slack: Not Connected</span>;
  return <span style={{ color: 'green' }}>Slack Connected{workspace ? ` (${workspace})` : ''}</span>;
}