import React, { useState, useEffect } from 'react';
import { IntegrationService, type NotionDatabase } from '../../services/integrationService';
import './NotionIntegration.css';

interface NotionConfigProps {
  integrationId: string;
  onSave: (dbId: string) => void;
}

const NotionIntegration: React.FC<NotionConfigProps> = ({ integrationId, onSave }) => {
  const [loading, setLoading] = useState<boolean>(true);
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
      console.error("Error fetching Notion databases", err);
      setError("Failed to load Notion databases. Please check your integration settings.");
    } finally {
      setLoading(false);
    }
  };

  const handleSave = async () => {
    if (!selectedDbId) {
      setError("Please select a database first");
      return;
    }

    setLoading(true);
    setError(null);

    try {
      await IntegrationService.saveNotionDatabase(selectedDbId);
      onSave(selectedDbId);
    } catch (err) {
      console.error("Error saving Notion database selection", err);
      setError("Failed to save database selection. Please try again.");
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

  if (databases.length === 0) {
    return (
      <div className="notion-integration-container">
        <div className="notion-integration-header">
          <h2>Notion Integration</h2>
        </div>
        <div className="notion-integration-content empty-state">
          <p>
            No databases found in your Notion workspace. Please make sure you have access to databases in your Notion account.
          </p>
          <button 
            className="retry-button" 
            onClick={fetchDatabases} 
            disabled={loading}
          >
            Retry
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="notion-integration-container">
      <div className="notion-integration-header">
        <h2>Notion Integration</h2>
      </div>
      <div className="notion-integration-content">
        {error && (
          <div className="error-message">
            {error}
          </div>
        )}

        <div className="form-control">
          <label htmlFor="notion-database-select">Select a Notion Database</label>
          
          <select 
            id="notion-database-select"
            value={selectedDbId}
            onChange={(e) => setSelectedDbId(e.target.value)}
          >
            {databases.map((db) => (
              <option key={db.id} value={db.id}>
                {db.title || "Untitled Database"}
              </option>
            ))}
          </select>
          
          <span className="caption">
            This database will be used for data integration with Apexo
          </span>
        </div>

        <div className="button-container">
          <button onClick={handleSave} disabled={loading}>
            Save Configuration
          </button>
        </div>
      </div>
    </div>
  );
};

export default NotionIntegration;