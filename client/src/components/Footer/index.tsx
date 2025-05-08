import './Footer.css';
import React from 'react';
import logo from '../../assets/images/apexo_logo.svg';

const Footer: React.FC = () => {
    const footerColumns = [
        {
            title: 'Product',
            links: [
                { name: 'Overview', url: '#' },
                { name: 'Features', url: '#features' },
                { name: 'Solutions', url: '#' },
                { name: 'Tutorials', url: '#' },
                { name: 'Pricing', url: '#pricing' }
            ]
        },
        {
            title: 'Company',
            links: [
                { name: 'About us', url: '#' },
                { name: 'Careers', url: '#' },
                { name: 'Press', url: '#' },
                { name: 'News', url: '#' },
                { name: 'Contact', url: '#' }
            ]
        },
        {
            title: 'Resources',
            links: [
                { name: 'Blog', url: '#' },
                { name: 'Newsletter', url: '#' },
                { name: 'Events', url: '#' },
                { name: 'Help centre', url: '#' },
                { name: 'Support', url: '#' }
            ]
        },
        {
            title: 'Legal',
            links: [
                { name: 'Terms', url: '#' },
                { name: 'Privacy', url: '#' },
                { name: 'Cookies', url: '#' },
                { name: 'Licenses', url: '#' },
                { name: 'Settings', url: '#' }
            ]
        }
    ];

    const socialIcons = [
        { icon: 'facebook', url: '#' },
        { icon: 'twitter', url: '#' },
        { icon: 'linkedin', url: '#' },
        { icon: 'github', url: '#' }
    ];

    return (
        <footer className="footer">
            <div className="footer-container">
                <div className="footer-main">
                    <div className="footer-info">
                        <div className="footer-logo">
                            <img src={logo} alt="Apexo Logo" />
                            <span>Apexo</span>
                        </div>
                        <p className="footer-description">
                            AI-powered assistant for streamlined meetings, task management, workflow automation, and hiring.
                        </p>
                        <div className="footer-social">
                            {socialIcons.map((social, index) => (
                                <a 
                                    key={index} 
                                    href={social.url} 
                                    className="social-icon" 
                                    aria-label={social.icon}
                                >
                                    <div className="icon-placeholder">{social.icon[0].toUpperCase()}</div>
                                </a>
                            ))}
                        </div>
                    </div>
                    
                    <div className="footer-links-container">
                        {footerColumns.map((column, index) => (
                            <div key={index} className="footer-column">
                                <h3 className="footer-title">{column.title}</h3>
                                <ul className="footer-links">
                                    {column.links.map((link, linkIndex) => (
                                        <li key={linkIndex}>
                                            <a href={link.url} className="footer-link">
                                                {link.name}
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                </div>
                
                <div className="footer-bottom">
                    <div className="footer-copyright">
                        &copy; {new Date().getFullYear()} Apexo. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
    );
};

export default Footer;