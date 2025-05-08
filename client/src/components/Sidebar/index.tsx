import './Sidebar.css';
import React from 'react';
import helpIcon from '../../assets/images/help_icon.png';
import burgerIcon from '../../assets/images/burger_icon.png';
import settingsIcon from '../../assets/images/settings_icon.png';

const Sidebar: React.FC = () => {
  return (
    <div className="sidebar">
      <div className="sidebar-top">
        <button className="menu-toggle">
          <img src={burgerIcon} alt="Menu" />
        </button>
      </div>
      
      <div className="sidebar-bottom">
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