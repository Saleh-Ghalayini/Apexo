import './Header.css';
import Button from '../Button';
import React, { useState } from 'react';
import logo from '../../assets/images/apexo_logo.svg';
import { Link } from 'react-router-dom';

const Header: React.FC = () => {
    
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    const scrollToSection = (sectionId: string) => (event: React.MouseEvent) => {
        event.preventDefault();
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({ behavior: 'smooth' });
        }
        
        if (mobileMenuOpen) {
            setMobileMenuOpen(false);
        }
    };

    return (
        <header>
            <button 
                className='mobile-menu-button'
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                aria-label="Toggle mobile menu"
            >
                {mobileMenuOpen ? '✕' : '☰'}
            </button>

            <div className='logo'>
                <img src={logo} alt="Apexo Logo" />
                <h1>Apexo</h1>
            </div>
            
            <nav className='nav'>
                <ul>
                    <li><a href='#' onClick={scrollToSection('about')}>About</a></li>
                    <li><a href='#' onClick={scrollToSection('features')}>Features</a></li>
                    <li><a href='#' onClick={scrollToSection('pricing')}>Pricing</a></li>
                </ul>
            </nav>
            
            <div className='auth'>
                <Link to="/login">
                    <Button className='btn login-btn'>Log in</Button>
                </Link>
                <Link to="/signup">
                    <Button className='btn start-btn'>Get Started</Button>
                </Link>
            </div>

            <div className={`mobile-menu ${mobileMenuOpen ? 'open' : ''}`}>
                <ul>
                    <li><a href='#' onClick={scrollToSection('about')}>About</a></li>
                    <li><a href='#' onClick={scrollToSection('features')}>Features</a></li>
                    <li><a href='#' onClick={scrollToSection('pricing')}>Pricing</a></li>
                    <li><Link to="/login" onClick={() => setMobileMenuOpen(false)}>Log in</Link></li>
                    <li><Link to="/signup" onClick={() => setMobileMenuOpen(false)}>Get Started</Link></li>
                </ul>
            </div>
        </header>
    );
}

export default Header;