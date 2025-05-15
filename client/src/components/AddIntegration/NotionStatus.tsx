import { useState } from 'react';

export default function NotionStatus() {
  const [status, setStatus] = useState<'connected' | 'not_connected' | 'loading'>('loading');

  if (status === 'loading') {
    return <div>Checking Notion connection status...</div>;
  }

  if (status === 'connected') {
    return <div>Connected to Notion</div>;
  }

  return <div>Not connected to Notion</div>;
}