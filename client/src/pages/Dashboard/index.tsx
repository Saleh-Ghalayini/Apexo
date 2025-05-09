import './Dashboard.css';
import React, { useState, useRef, useEffect } from 'react';
import Sidebar from '../../components/Sidebar';
import Message from '../../components/Message';
import addIcon from '../../assets/images/add_icon.png';
import userIcon from '../../assets/images/user_icon.png';
import arrowIcon from '../../assets/images/arrow_icon.png';

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
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLTextAreaElement>(null);
  
  // Scrolling to the bottom of the chat when a new message is sent to the chat
  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
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
        text: "This is a simulated AI response. In a real application, this would come from the backend API.",
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
  
  return (
    <div className="dashboard-container">
      <Sidebar />
      
      <div className="dashboard-content">
        <div className="dashboard-header">
          <button className="try-advanced-btn">Try Apexo Advanced</button>
          <div className="user-avatar">
            <img src={userIcon} width='20px' height='20px' alt="User" />
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