import './Dashboard.css';
import React, { useState } from 'react';
import Sidebar from '../../components/Sidebar';
import userIcon from '../../assets/images/user_icon.png';
import arrowIcon from '../../assets/images/arrow_icon.png';

const Dashboard: React.FC = () => {
  const [username, setUsername] = useState('Username');
  
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
          <h1 className="welcome-message">
            Welcome Back, <span className="username">{username}</span>
          </h1>
        </div>
        
        <div className="dashboard-chat">
          <div className="chat-input-container">
            <input 
              type="text" 
              placeholder="Type a command or ask Apexo for assistance..." 
              className="chat-input"
            />
            <button className="send-button">
              <img src={arrowIcon} alt='send' />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;