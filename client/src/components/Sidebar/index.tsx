import './Sidebar.css';
import React, { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import addIcon from '../../assets/images/add_icon.png';
import helpIcon from '../../assets/images/help_icon.png';
import linesIcon from '../../assets/images/lines_icon.png';
import apexoLogo from '../../assets/images/apexo_logo.svg';
import burgerIcon from '../../assets/images/burger_icon.png';
import searchIcon from '../../assets/images/search_icon.png';
import integrationIcon from '../../assets/images/integrations_icon.png';
import { ChatService } from '../../services/chatService';
import type { ChatSession } from '../../services/chatService';

interface SidebarProps {
  onSelectSession?: (sessionId: string) => void;
  onNewChat?: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ onSelectSession, onNewChat }) => {
  const [isExpanded, setIsExpanded] = useState(false);
  const [sessions, setSessions] = useState<ChatSession[]>([]);
  const navigate = useNavigate();

  const fetchSessions = useCallback(async () => {
    try {
      const data = await ChatService.getSessions();
      setSessions(Array.isArray(data) ? data : []);
    } catch {
      setSessions([]);
    }
  }, []);

  useEffect(() => {
    fetchSessions();
  }, [fetchSessions]);

  const groupSessionsByDate = (sessions: ChatSession[]) => {
    if (!Array.isArray(sessions)) return {};
    const groups: { [key: string]: ChatSession[] } = {};
    const today = new Date().toDateString();
    const yesterday = new Date(Date.now() - 86400000).toDateString();
    sessions.forEach(session => {
      const sessionDate = new Date(session.created_at).toDateString();
      let group = 'Previous 7 Days';
      if (sessionDate === today) group = 'Today';
      else if (sessionDate === yesterday) group = 'Yesterday';
      if (!groups[group]) groups[group] = [];
      groups[group].push(session);
    });
    return groups;
  };

  const grouped = groupSessionsByDate(sessions);

  const toggleSidebar = () => {
    setIsExpanded(!isExpanded);
  };
  
  const navigateToIntegrations = () => {
    navigate('/integrations');
  };

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
            <button className="new-chat-button" onClick={onNewChat}>
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
              {Object.entries(grouped).map(([date, chats]) => (
                <div key={date} className="chat-group">
                  <div className="chat-date">{date}</div>
                  {(chats as ChatSession[]).map((chat) => (
                    <button key={chat.id} className="chat-item" onClick={() => onSelectSession?.(chat.id)}>
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
        <button className="sidebar-icon-btn" onClick={navigateToIntegrations}>
          <img src={integrationIcon} alt="Integrations" />
          {isExpanded && <span>Integrations</span>}
        </button>
      </div>
    </div>
  );
};

export default Sidebar;