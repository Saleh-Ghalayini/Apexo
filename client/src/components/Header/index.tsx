import logo from '../../assets/images/apexo_logo.svg';
import React from 'react';
import './Header.css';

const Header: React.FC = () => {

    return (
        <header>
            <div className='logo'>
                <img src={logo} />
                <h1>Apexo</h1>
            </div>
            <div className='nav'>
                <ul>
                    <li><a href='#about'>About</a></li>
                    <li><a href='#features'>Features</a></li>
                    <li><a href='#pricing'>Pricing</a></li>
                </ul>
            </div>
            <div className='auth'>
                <button className='login-btn'>Login</button>
                <button className='start-bth'>Get Started</button>
            </div>
        </header>
    );
}

export default Header;