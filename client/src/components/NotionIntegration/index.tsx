import React, { useState, useEffect } from 'react';
import { IntegrationService, type NotionDatabase } from '../../services/integrationService';

interface NotionConfigProps {
  integrationId: string;
  onSave: (dbId: string) => void;
}

const NotionIntegration: React.FC<NotionConfigProps> = ({ integrationId, onSave }) => {
  const [loading, setLoading] = useState(true);
  const [databases, setDatabases] = useState<NotionDatabase[]>([]);
  const [selectedDbId, setSelectedDbId] = useState<string>('');
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
      if (dbs.length > 0) {
        setSelectedDbId(dbs[0].id);
      }
    } catch (err) {
      setError('Failed to load Notion databases.');
    } finally {
      setLoading(false);
    }
  };

  const handleSave = () => {
    if (!selectedDbId) {
      setError('Please select a database first');
      return;
    }
    onSave(selectedDbId);
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
      <select
        value={selectedDbId}
        onChange={(e) => setSelectedDbId(e.target.value)}
      >
        {databases.map((db) => (
          <option key={db.id} value={db.id}>
            {db.title || 'Untitled Database'}
          </option>
        ))}
      </select>
      <button onClick={handleSave}>Save Configuration</button>
    </div>
  );
};

export default NotionIntegration;