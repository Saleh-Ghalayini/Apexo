import React, { useState, useRef, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

interface ChatMessage {
  id: number;
  text: string;
  isUser: boolean;
  timestamp: Date;
}

const Sidebar = ({ onSelectSession, onNewChat }: { onSelectSession: (id: string) => void; onNewChat: () => void }) => (
  <div style={{ width: 200, background: '#eee' }}>
    <button onClick={onNewChat}>New Chat</button>
    <button onClick={() => onSelectSession('session-1')}>Session 1</button>
  </div>
);
const Message = ({ text, isUser }: { text: string; isUser: boolean }) => (
  <div style={{ textAlign: isUser ? 'right' : 'left' }}>{text}</div>
);

const Dashboard: React.FC = () => {
  const navigate = useNavigate();
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [inputValue, setInputValue] = useState('');
  const [showProfileDropdown, setShowProfileDropdown] = useState(false);
  const [profilePicture, setProfilePicture] = useState<string | null>(null);
  const [sessionId, setSessionId] = useState<string | null>(null);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  const handleSend = () => {
    if (!inputValue.trim()) return;
    setMessages([
      ...messages,
      {
        id: Date.now(),
        text: inputValue,
        isUser: true,
        timestamp: new Date()
      }
    ]);
    setInputValue('');
  };

  const handleLogout = () => {
    navigate('/login');
  };

  const handleNewChat = () => {
    setSessionId(null);
    setMessages([]);
  };

  const handleSelectSession = (selectedSessionId: string) => {
    setSessionId(selectedSessionId);
    setMessages([]);
  };

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  return (
    <div style={{ display: 'flex' }}>
      <Sidebar onSelectSession={handleSelectSession} onNewChat={handleNewChat} />
      <div style={{ flex: 1 }}>
        <div onClick={() => setShowProfileDropdown(!showProfileDropdown)}>
          {profilePicture ? (
            <img src={profilePicture} alt="Profile" />
          ) : (
            <span>User</span>
          )}
        </div>
        {showProfileDropdown && (
          <div>
            <div>Profile Dropdown</div>
            <button onClick={handleLogout}>Logout</button>
          </div>
        )}
        <div>
          {messages.map((msg) => (
            <Message key={msg.id} text={msg.text} isUser={msg.isUser} />
          ))}
          <div ref={messagesEndRef} />
        </div>
        <input
          value={inputValue}
          onChange={e => setInputValue(e.target.value)}
          placeholder="Type a message"
        />
        <button onClick={handleSend}>Send</button>
      </div>
    </div>
  );
};

export default Dashboard;