import { useEffect, useState } from 'react';
import { IntegrationService } from '../../services/integrationService';
import type { Integration } from '../../services/integrationService';

export default function NotionStatus() {
  const [status, setStatus] = useState<'connected' | 'not_connected' | 'loading'>('loading');
  const [workspaceName, setWorkspaceName] = useState<string | null>(null);

  useEffect(() => {
    async function checkStatus() {
      try {
        const integrations: Integration[] = await IntegrationService.getIntegrations();
        const notionIntegration = integrations.find(
          (i) => i.provider === 'notion' && i.status === 'active'
        );
        if (notionIntegration) {
          setStatus('connected');
          // @ts-expect-error: metadata may exist on backend response
          if (notionIntegration.metadata?.workspace_name) {
            // @ts-expect-error: metadata may exist on backend response
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
      <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
        <div
          style={{
            width: '8px',
            height: '8px',
            borderRadius: '50%',
            backgroundColor: '#36B37E',
          }}
        ></div>
        <span>
          Connected to <strong>{workspaceName || 'Notion'}</strong>
        </span>
      </div>
    );
  }

  return (
    <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
      <div
        style={{
          width: '8px',
          height: '8px',
          borderRadius: '50%',
          backgroundColor: '#FF5630',
        }}
      ></div>
      <span>Not connected to Notion</span>
    </div>
  );
}