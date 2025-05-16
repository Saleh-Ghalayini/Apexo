import React, { useState, useEffect } from 'react';
import { IntegrationService, type NotionDatabase } from '../../services/integrationService';

interface NotionConfigProps {
  integrationId: string;
  onSave: (dbId: string) => void;
}

const NotionIntegration: React.FC<NotionConfigProps> = ({ integrationId, onSave }) => {
  const [loading, setLoading] = useState(true);
  const [databases, setDatabases] = useState<NotionDatabase[]>([]);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchDatabases();
  }, [integrationId]);

  const fetchDatabases = async () => {
    setLoading(true);
    setError(null);
    try {
      const dbs = await IntegrationService.getNotionDatabases();
      setDatabases(dbs);
    } catch (err) {
      setError('Failed to load Notion databases.');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div>
        <h2>Notion Integration</h2>
        <div className="error-message">{error}</div>
        <button onClick={fetchDatabases}>Retry</button>
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