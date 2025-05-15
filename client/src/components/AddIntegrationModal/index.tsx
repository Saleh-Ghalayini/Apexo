import React, { useState } from 'react';

interface AddIntegrationModalProps {
  show: boolean;
  onClose: () => void;
  onIntegrationAdded: () => void;
}

const AddIntegrationModal: React.FC<AddIntegrationModalProps> = ({ show, onClose }) => {
  const [providers, setProviders] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  if (!show) return null;
  return (
    <div>
      <div>
        <h2>Add Integration</h2>
        <button onClick={onClose}>Close</button>
      </div>
      <div>
        {loading && <div>Loading...</div>}
        {error && <div>{error}</div>}
      </div>
    </div>
  );
};

export default AddIntegrationModal;