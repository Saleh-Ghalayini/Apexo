import './Login.css';
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import logo from '../../../assets/images/apexo_logo.svg';
import illustration from '../../../assets/images/illustration.png';
import mailIcon from '../../../assets/images/mail_icon.png';
import lockIcon from '../../../assets/images/lock_icon.png';

const Login: React.FC = () => {

  const [formData, setFormData] = useState({
    email: '',
    password: '',
    rememberMe: false,
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setFormData(prevState => ({
      ...prevState,
      [name]: type === 'checkbox' ? checked : value,
    }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    // logic will be implemented later, focusing on UI for now
    console.log('Login form submitted:', formData);
  };

  return (
    <div className="login-container">
      <div className="login-form-container">
        <div className="login-header">
          <div className="login-logo">
            <img src={logo} alt="Logo" style={{ width: '55px', height: '55px' }} />
            <span>Apexo</span>
          </div>
          <div className="create-account-link">
            <Link to="/signup">Create an account</Link>
          </div>
        </div>

        <div className="login-form-wrapper">
          <div className="form-field">
            <label htmlFor="email">Email</label>
            <div className="input-wrapper">
              <img src={mailIcon} alt="Email" className="input-icon" />
              <input
                type="email"
                id="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="Example@gmail.com"
                required
              />
            </div>
          </div>
          
          <div className="form-field">
            <label htmlFor="password">Password</label>
            <div className="input-wrapper">
              <img src={lockIcon} alt="Password" className="input-icon" />
              <input
                type="password"
                id="password"
                name="password"
                value={formData.password}
                onChange={handleChange}
                placeholder="ApexoP@123"
                required
              />
            </div>
          </div>
          
          <div className="form-options">
            <div className="remember-me">
              <input
                type="checkbox"
                id="rememberMe"
                name="rememberMe"
                checked={formData.rememberMe}
                onChange={handleChange}
              />
              <label htmlFor="rememberMe">Remember me</label>
            </div>
            <Link to="/forgot-password" className="forgot-password">
              Forgot password
            </Link>
          </div>
          
          <button type="submit" className="sign-in-button" onClick={handleSubmit}>
            Sign in
          </button>
          
          {/* Mobile-only display for create an account link */}
          <div className="mobile-create-account">
            <Link to="/signup">Create an account</Link>
          </div>
        </div>

        <div className="trouble-signing">
          <Link to="/help">Trouble signing in?</Link>
        </div>
      </div>

      <div className="login-illustration">
        <img src={illustration} alt="Login illustration" />
      </div>
    </div>
  );
};

export default Login;