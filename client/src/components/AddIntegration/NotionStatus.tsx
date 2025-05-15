import { useEffect, useState } from 'react';
import { IntegrationService } from '../../services/integrationService';

export default function NotionStatus() {
  const [status, setStatus] = useState<'connected' | 'not_connected' | 'loading'>('loading');
  const [workspaceName, setWorkspaceName] = useState<string | null>(null);

  useEffect(() => {
    async function checkStatus() {
      try {
        const integrations = await IntegrationService.getIntegrations();
        const notionIntegration = integrations.find(
          (i) => i.provider === 'notion' && i.status === 'active'
        );
        if (notionIntegration) {
          setStatus('connected');
          if (notionIntegration.metadata?.workspace_name) {
            setWorkspaceName(notionIntegration.metadata.workspace_name);
          }
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
    return (
      <div>
        Connected to <strong>{workspaceName || 'Notion'}</strong>
      </div>
    );
  }

  return <div>Not connected to Notion</div>;
}