import React, { useState } from 'react';

export default function SlackStatus() {
  const [connected, setConnected] = useState(false);
  const [workspace, setWorkspace] = useState<string | null>(null);

  if (!connected) return <span style={{ color: 'red' }}>Slack: Not Connected</span>;
  return <span style={{ color: 'green' }}>Slack Connected{workspace ? ` (${workspace})` : ''}</span>;
}