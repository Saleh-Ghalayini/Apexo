import React, { useState } from 'react';
import apexoLogo from '../../assets/images/apexo_logo.svg';
import addIcon from '../../assets/images/add_icon.png';

interface SidebarProps {
  onSelectSession?: (sessionId: string) => void;
  onNewChat?: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ onNewChat }) => {
  const [isExpanded, setIsExpanded] = useState(false);

  return (
    <div className={`sidebar ${isExpanded ? 'expanded' : ''}`}>
      <button onClick={() => setIsExpanded(!isExpanded)}>
        {isExpanded ? 'Collapse' : 'Expand'}
      </button>
      {isExpanded && (
        <div>
          <img src={apexoLogo} alt="Apexo Logo" />
          <button onClick={onNewChat}>
            <img src={addIcon} alt="New Chat" />
            New Chat
          </button>
        </div>
      )}
    </div>
  );
};

export default Sidebar;