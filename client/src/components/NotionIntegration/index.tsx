import React from 'react';

interface NotionConfigProps {
  integrationId: string;
  onSave: (dbId: string) => void;
}

const NotionIntegration: React.FC<NotionConfigProps> = () => {
  return (
    <div>
      <h2>Notion Integration</h2>
      <p>Configure your Notion integration here.</p>
    </div>
  );
};

export default NotionIntegration;