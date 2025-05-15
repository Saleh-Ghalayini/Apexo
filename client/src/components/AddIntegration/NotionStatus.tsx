import { useEffect, useState } from 'react';
import { IntegrationService } from '../../services/integrationService';

export default function NotionStatus() {
  const [status, setStatus] = useState<'connected' | 'not_connected' | 'loading'>('loading');

  useEffect(() => {
    async function checkStatus() {
      try {
        const integrations = await IntegrationService.getIntegrations();
        const notionIntegration = integrations.find(
          (i) => i.provider === 'notion' && i.status === 'active'
        );
        if (notionIntegration) {
          setStatus('connected');
        } else {
          setStatus('not_connected');
        }
      } catch {
        setStatus('not_connected');
      }
    }
    checkStatus();
  }, []);

  if (status === 'loading') {
    return <div>Checking Notion connection status...</div>;
  }

  if (status === 'connected') {
    return <div>Connected to Notion</div>;
  }

  return <div>Not connected to Notion</div>;
}