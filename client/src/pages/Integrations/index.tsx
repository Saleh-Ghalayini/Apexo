import React from 'react';
import { useNavigate } from 'react-router-dom';
import './Integrations.css';

interface IntegrationAccount {
  id: string;
  name: string;
  email: string;
  type: 'workspace' | 'channel' | 'scheduler';
  status: 'active' | 'inactive';
  linkingDate: string;
}

const IntegrationsPage: React.FC = () => {
  const navigate = useNavigate();
  
  const handleGoBack = () => {
    navigate('/dashboard');
  };

  return (
    <div className="integrations-container">
      <div className="integrations-header">
        <button className="back-button" onClick={handleGoBack}>
          ←
        </button>
        <h1 className="header-title">Currently Linked Accounts</h1>
      </div>
      
      <div className="integrations-content">
        <p>No accounts linked yet.</p>
      </div>
    </div>
  );
};

export default IntegrationsPage;