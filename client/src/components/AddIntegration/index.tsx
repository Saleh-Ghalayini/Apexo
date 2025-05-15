import React, { useState } from 'react';
import Modal from '../Modal';

interface AddIntegrationProps {
  isOpen: boolean;
  onClose: () => void;
  onAddIntegration: (integration: {
    name: string;
    email: string;
    type: string;
  }) => void;
}

const AddIntegration: React.FC<AddIntegrationProps> = ({ isOpen, onClose }) => {
  const [selectedType, setSelectedType] = useState('');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');

  if (!isOpen) return null;

  return (
    <Modal isOpen={isOpen} onClose={onClose} title="Add New Integration">
    </Modal>
  );
};

export default AddIntegration;