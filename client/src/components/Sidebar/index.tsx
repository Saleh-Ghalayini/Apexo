import './Sidebar.css';
import React, { useState } from 'react';
import addIcon from '../../assets/images/add_icon.png';
import helpIcon from '../../assets/images/help_icon.png';
import linesIcon from '../../assets/images/lines_icon.png';
import apexoLogo from '../../assets/images/apexo_logo.svg';
import burgerIcon from '../../assets/images/burger_icon.png';
import searchIcon from '../../assets/images/search_icon.png';
import integrationIcon from '../../assets/images/integrations_icon.png';

const Sidebar: React.FC = () => {
  const [isExpanded, setIsExpanded] = useState(false);

  const toggleSidebar = () => {
    setIsExpanded(!isExpanded);
  };

  // Sample chat data grouped by date
  const chatGroups = [
    {
      date: 'Today',
      chats: [
        { id: 1, title: 'Training dataset analysis' },
        { id: 2, title: 'Marketing strategy planning' }
      ]
    },
    {
      date: 'Yesterday',
      chats: [
        { id: 3, title: 'Code review for API endpoints' },
        { id: 4, title: 'Customer feedback analysis' }
      ]
    },
    {
      date: 'Previous 7 Days',
      chats: [
        { id: 5, title: 'Product launch timeline' },
        { id: 6, title: 'Budget forecasting for Q3' }
      ]
    }
  ];

  return (
    <div className={`sidebar ${isExpanded ? 'expanded' : ''}`}>
      <div className="sidebar-top">
        <button className="menu-toggle" onClick={toggleSidebar}>
          <img src={burgerIcon} alt="Menu" />
        </button>
        
        {isExpanded && (
          <div className="sidebar-logo">
            <img src={apexoLogo} alt="Apexo Logo" />
            <span>Apexo</span>
          </div>
        )}
      </div>
      
      {isExpanded && (
        <>
          <div className="new-chat-container">
            <button className="new-chat-button">
              <img src={addIcon} alt="New Chat" />
              <span>New Chat</span>
            </button>
          </div>
          
          <div className="chat-section">
            <div className="chat-section-header">
              <h3>Recent Chats</h3>
              <button className="search-button">
                <img src={searchIcon} alt="Search" />
              </button>
            </div>
            
            <div className="chat-list">
              {chatGroups.map((group) => (
                <div key={group.date} className="chat-group">
                  <div className="chat-date">{group.date}</div>
                  {group.chats.map((chat) => (
                    <button key={chat.id} className="chat-item">
                      <img src={linesIcon} alt="Chat" />
                      <span>{chat.title}</span>
                    </button>
                  ))}
                </div>
              ))}
            </div>
          </div>
        </>
      )}
      
      <div className="sidebar-bottom">
        <button className="sidebar-icon-btn">
          <img src={helpIcon} alt="Help" />
          {isExpanded && <span>Help</span>}
        </button>
        <button className="sidebar-icon-btn">
          <img src={integrationIcon} alt="Integrations" />
          {isExpanded && <span>Integrations</span>}
        </button>
      </div>
    </div>
  );
};

export default Sidebar;