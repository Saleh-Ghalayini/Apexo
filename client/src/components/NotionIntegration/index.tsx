import React, { useState } from 'react';

interface NotionConfigProps {
  integrationId: string;
  onSave: (dbId: string) => void;
}

const NotionIntegration: React.FC<NotionConfigProps> = () => {
  const [loading, setLoading] = useState(true);

  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
      </div>
    );
  }

  return (
    <div>
      <h2>Notion Integration</h2>
      <p>Configure your Notion integration here.</p>
    </div>
  );
};

export default NotionIntegration;