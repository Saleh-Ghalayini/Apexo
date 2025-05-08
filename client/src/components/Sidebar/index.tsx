import './Sidebar.css';
import React, { useState } from 'react';
import addIcon from '../../assets/images/add_icon.png';
import helpIcon from '../../assets/images/help_icon.png';
import linesIcon from '../../assets/images/lines_icon.png';
import burgerIcon from '../../assets/images/burger_icon.png';
import searchIcon from '../../assets/images/search_icon.png';
import integrationsIcon from '../../assets/images/integrations_icon.png';

// Example chat data for demonstration
const chatsByDate = {
  'Today': [
    { id: 1, title: 'Product meeting notes' },
    { id: 2, title: 'UI design feedback' }
  ],
  'Yesterday': [
    { id: 3, title: 'Marketing strategy' },
    { id: 4, title: 'Project timeline' }
  ],
  'Previous 7 Days': [
    { id: 5, title: 'Budget planning' },
    { id: 6, title: 'Q3 roadmap' }
  ]
};

const Sidebar: React.FC = () => {
  const [isExpanded, setIsExpanded] = useState(false);

  const toggleSidebar = () => {
    setIsExpanded(!isExpanded);
  };

  return (
    <div className={`sidebar ${isExpanded ? 'expanded' : ''}`}>
      <div className="sidebar-top">
        <button className="menu-toggle" onClick={toggleSidebar}>
          <img src={burgerIcon} alt="Menu" />
        </button>
        
        {isExpanded && (
          <div className="sidebar-logo">
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
              <h3>Chats</h3>
              <button className="search-button">
                <img src={searchIcon} alt="Search" />
              </button>
            </div>

            <div className="chat-list">
              {Object.entries(chatsByDate).map(([date, chats]) => (
                <div key={date} className="chat-group">
                  <h4 className="chat-date">{date}</h4>
                  {chats.map(chat => (
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
        <button className="sidebar-icon-btn integrations-btn">
          <img src={integrationsIcon} alt="Integrations" />
          {isExpanded && <span>Integrations</span>}
        </button>
      </div>
    </div>
  );
};

export default Sidebar;