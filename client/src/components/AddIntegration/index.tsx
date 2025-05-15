import React, { useState } from 'react';
import Modal from '../Modal';
import slackIcon from '../../assets/images/w_slack_icon.png';
import calendarIcon from '../../assets/images/calendar_icon.png';
import notionIcon from '../../assets/images/notion_icon.png';
import mailIcon from '../../assets/images/w_mail_icon.png';

interface AddIntegrationProps {
  isOpen: boolean;
  onClose: () => void;
  onAddIntegration: (integration: {
    name: string;
    email: string;
    type: string;
  }) => void;
}

const providers = [
  { id: 'slack', name: 'Slack', icon: slackIcon, type: 'workspace' },
  { id: 'notion', name: 'Notion', icon: notionIcon, type: 'channel' },
  { id: 'calendar', name: 'Google Calendar', icon: calendarIcon, type: 'scheduler' },
  { id: 'email', name: 'Email', icon: mailIcon, type: 'Email' }
];

const AddIntegration: React.FC<AddIntegrationProps> = ({ isOpen, onClose }) => {
  const [selectedType, setSelectedType] = useState('');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');

  if (!isOpen) return null;

  return (
    <Modal isOpen={isOpen} onClose={onClose} title="Add New Integration">
      <div>
        <label>Integration Type</label>
        <div>
          {providers.map(type => (
            <button key={type.id} onClick={() => setSelectedType(type.id)}>
              <img src={type.icon} alt={type.name} width={24} height={24} />
              {type.name}
            </button>
          ))}
        </div>
      </div>
    </Modal>
  );
};

export default AddIntegration;