import React from 'react';

interface AddIntegrationProps {
  isOpen: boolean;
  onClose: () => void;
  onAddIntegration: (integration: {
    name: string;
    email: string;
    type: string;
  }) => void;
}

const AddIntegration: React.FC<AddIntegrationProps> = ({ isOpen, onClose, onAddIntegration }) => {
  return null;
};

export default AddIntegration;