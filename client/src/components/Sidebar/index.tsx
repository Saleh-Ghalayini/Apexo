import './Sidebar.css';
import React from 'react';
import helpIcon from '../../assets/images/help_icon.png';
import burgerIcon from '../../assets/images/burger_icon.png';
import integrationsIcon from '../../assets/images/integrations_icon.png';

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
        <button className="sidebar-icon-btn integrations-btn">
          <img src={integrationsIcon} alt="Integrations" />
        </button>
      </div>
    </div>
  );
};

export default Sidebar;