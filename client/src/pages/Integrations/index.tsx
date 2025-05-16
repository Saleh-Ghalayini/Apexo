import React, { useState, useEffect } from 'react';

const IntegrationsPage: React.FC = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [accounts, setAccounts] = useState<any[]>([]);
  const [isModalOpen, setIsModalOpen] = useState(false);

  useEffect(() => {
    setTimeout(() => {
      setLoading(false);
      setAccounts([{ id: '1', name: 'Test Integration' }]);
    }, 1000);
  }, []);

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      <h1>Integrations</h1>
      <button onClick={() => setIsModalOpen(true)}>Add Integration</button>
      <ul>
        {accounts.map(acc => (
          <li key={acc.id}>{acc.name}</li>
        ))}
      </ul>
      {isModalOpen && <div>Modal Placeholder</div>}
    </div>
  );
};

export default IntegrationsPage;