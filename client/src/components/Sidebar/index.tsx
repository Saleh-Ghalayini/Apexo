import './Sidebar.css';
import React from 'react';
import burgerIcon from '../../assets/images/burger_icon.png';
import helpIcon from '../../assets/images/help_icon.png';
import settingsIcon from '../../assets/images/settings_icon.png';

const Sidebar: React.FC = () => {
  return (
    <div className="sidebar">
      <button className="menu-toggle">
        <img src={burgerIcon} alt="Menu" />
      </button>
      
      <div className="sidebar-icons">
        <button className="sidebar-icon-btn">
          <img src={helpIcon} alt="Help" />
        </button>
        <button className="sidebar-icon-btn">
          <img src={settingsIcon} alt="Settings" />
        </button>
      </div>
    </div>
  );
};

export default Sidebar;