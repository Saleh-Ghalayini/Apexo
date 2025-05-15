import React from 'react';

interface AddIntegrationModalProps {
  show: boolean;
  onClose: () => void;
  onIntegrationAdded: () => void;
}

const AddIntegrationModal: React.FC<AddIntegrationModalProps> = ({ show, onClose }) => {
  if (!show) return null;
  return (
    <div>
      <div>
        <h2>Add Integration</h2>
        <button onClick={onClose}>Close</button>
      </div>
      <div>
      </div>
    </div>
  );
};

export default AddIntegrationModal;