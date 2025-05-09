import './Dashboard.css';
import Sidebar from '../../components/Sidebar';
import Message from '../../components/Message';
import addIcon from '../../assets/images/add_icon.png';
import userIcon from '../../assets/images/user_icon.png';
import React, { useState, useRef, useEffect } from 'react';
import arrowIcon from '../../assets/images/arrow_icon.png';
import logoutIcon from '../../assets/images/w_logout_icon.png';
import settingsIcon from '../../assets/images/settings_icon.png';
import helpIcon from '../../assets/images/help_icon.png';
import notifactionIcon from '../../assets/images/notification_icon.png';

interface ChatMessage {
  id: number;
  text: string;
  isUser: boolean;
  timestamp: Date;
}

const Dashboard: React.FC = () => {
  const [username, setUsername] = useState('Username');
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [inputValue, setInputValue] = useState('');
  const [showProfileDropdown, setShowProfileDropdown] = useState(false);
  const [profilePicture, setProfilePicture] = useState<string | null>(null);
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLTextAreaElement>(null);
  const dropdownRef = useRef<HTMLDivElement>(null);
  
  // Scrolling to the bottom of the chat when a new message is sent to the chat
  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  // Handle clicks outside of dropdown to close it
  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setShowProfileDropdown(false);
      }
    }

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  useEffect(() => {
    const savedProfilePicture = localStorage.getItem('profilePicture');
    if (savedProfilePicture) {
      setProfilePicture(savedProfilePicture);
    }
  }, []);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    setInputValue(e.target.value);
    
    if (inputRef.current) {
      inputRef.current.style.height = 'auto';
      inputRef.current.style.height = `${Math.min(inputRef.current.scrollHeight, 150)}px`;
    }
  };

  const handleSendMessage = () => {
    if (!inputValue.trim()) return;

    const newMessage: ChatMessage = {
      id: Date.now(),
      text: inputValue,
      isUser: true,
      timestamp: new Date()
    };
    
    setMessages(prev => [...prev, newMessage]);
    setInputValue('');

    if (inputRef.current) {
      inputRef.current.style.height = 'auto';
    }
    
    // Making AI response appear after 1 second
    setTimeout(() => {
      const aiResponse: ChatMessage = {
        id: Date.now() + 1,
        text: "I'm here to help with your business intelligence needs. Let me know what you'd like to analyze today.",
        isUser: false,
        timestamp: new Date()
      };
      setMessages(prev => [...prev, aiResponse]);
    }, 1000);
  };

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSendMessage();
    }
  };

  const toggleProfileDropdown = () => {
    setShowProfileDropdown(!showProfileDropdown);
  };

  return (
    <div className="dashboard-container">
      <Sidebar />
      
      <div className="dashboard-content">
        <div className="dashboard-header">
          <div className="user-avatar-container" ref={dropdownRef}>
            <div className="user-avatar" onClick={toggleProfileDropdown}>
              {profilePicture ? (
                <img 
                  src={profilePicture} 
                  className="profile-picture" 
                  alt="Profile" 
                />
              ) : (
                <img src={userIcon} width='20px' height='20px' alt="User" />
              )}
            </div>
            {showProfileDropdown && (
              <div className="profile-dropdown" ref={dropdownRef}>
                <div className="profile-header">
                  <div className="profile-name">{username || 'User'}</div>
                  <div className="profile-email">user@example.com</div>
                </div>
                
                <div className="dropdown-section">
                  <div className="dropdown-item">
                    <div className="dropdown-icon">
                    <img src={settingsIcon} width='16px' height='16px' alt="Logout" />
                    </div>
                    Account settings
                  </div>
                </div>
                
                <div className="dropdown-divider"></div>
                
                <div className="dropdown-section">
                  <div className="dropdown-item">
                    <div className="dropdown-icon">
                      <img src={notifactionIcon} width='16px' height='16px' alt="Profile" />
                    </div>
                    Notifications
                  </div>
                  
                  <div className="dropdown-item">
                    <div className="dropdown-icon">
                      <img src={helpIcon} width='16px' height='16px' alt="Help" />
                    </div>
                    Help & Support
                  </div>
                </div>
                
                <div className="dropdown-divider"></div>
                
                <div className="dropdown-section">
                  <div className="dropdown-item logout-item">
                    <div className="dropdown-icon">
                      <img src={logoutIcon} width='16px' height='16px' alt="Logout" />
                    </div>
                    Logout
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
        
        <div className="dashboard-main">
          {messages.length === 0 ? (
            <h1 className="welcome-message">
              Welcome Back, <span className="username">{username}</span>
            </h1>
          ) : (
            <div className="messages-container">
              {messages.map((message) => (
                <Message 
                  key={message.id}
                  text={message.text}
                  isUser={message.isUser}
                  timestamp={message.timestamp}
                />
              ))}
              <div ref={messagesEndRef} />
            </div>
          )}
        </div>
        
        <div className="dashboard-chat">
          <div className="chat-input-container">
            <img src={addIcon} alt='upload' />
            <textarea
              ref={inputRef}
              value={inputValue}
              onChange={handleInputChange}
              onKeyDown={handleKeyDown}
              placeholder="Type a command or ask Apexo for assistance..."
              className="chat-input"
              rows={1}
            />
            <button className="send-button" onClick={handleSendMessage}>
              <img src={arrowIcon} alt='send' />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;