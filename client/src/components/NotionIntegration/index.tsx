import React, { useState, useEffect } from 'react';
import { IntegrationService, type NotionDatabase } from '../../services/integrationService';

interface NotionConfigProps {
  integrationId: string;
  onSave: (dbId: string) => void;
}

const NotionIntegration: React.FC<NotionConfigProps> = ({ integrationId, onSave }) => {
  const [loading, setLoading] = useState(true);
  const [databases, setDatabases] = useState<NotionDatabase[]>([]);

  useEffect(() => {
    fetchDatabases();
  }, [integrationId]);

  const fetchDatabases = async () => {
    setLoading(true);
    const dbs = await IntegrationService.getNotionDatabases();
    setDatabases(dbs);
    setLoading(false);
  };

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
      <ul>
        {databases.map((db) => (
          <li key={db.id}>{db.title || 'Untitled Database'}</li>
        ))}
      </ul>
    </div>
  );
};

export default NotionIntegration;