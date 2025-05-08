import './Signup.css';
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import logo from '../../../assets/images/apexo_logo.svg';
import mailIcon from '../../../assets/images/mail_icon.png';
import lockIcon from '../../../assets/images/lock_icon.png';
import userIcon from '../../../assets/images/user_icon.png';
import companyIcon from '../../../assets/images/company_icon.png';
import illustration from '../../../assets/images/illustration.png';

const Signup: React.FC = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company_name: '',
    company_domain: '',
    role: 'employee',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prevState => ({
      ...prevState,
      [name]: value,
    }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    // logic will be implemented later, focusing on UI for now
    console.log('Signup form submitted:', formData);
  };

  return (
    <div className="signup-container">
      <div className="signup-form-container">
        <div className="signup-header">
          <div className="signup-logo">
            <img src={logo} alt="Logo" style={{ width: '55px', height: '55px' }} />
            <span>Apexo</span>
          </div>
          <div className="login-account-link">
            <Link to="/login">Already have an account</Link>
          </div>
        </div>

        <div className="signup-form-wrapper">
          <div className="form-field">
            <label htmlFor="name">Full Name</label>
            <div className="input-wrapper">
              <img src={userIcon} alt="User" className="input-icon" />
              <input
                type="text"
                id="name"
                name="name"
                value={formData.name}
                onChange={handleChange}
                placeholder="John Doe"
                required
              />
            </div>
          </div>
          
          <div className="form-field">
            <label htmlFor="email">Email Address</label>
            <div className="input-wrapper">
              <img src={mailIcon} alt="Email" className="input-icon" />
              <input
                type="email"
                id="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                placeholder="example@company.com"
                required
              />
            </div>
          </div>

          <div className="form-field">
            <label htmlFor="company_name">Company Name</label>
            <div className="input-wrapper">
              <img src={companyIcon} alt="Company" className="input-icon" />
              <input
                type="text"
                id="company_name"
                name="company_name"
                value={formData.company_name}
                onChange={handleChange}
                placeholder="Acme Inc."
                required
              />
            </div>
          </div>

          <div className="form-field">
            <label htmlFor="company_domain">Company Domain</label>
            <div className="input-wrapper">
              <img src={companyIcon} alt="Domain" className="input-icon" />
              <input
                type="text"
                id="company_domain"
                name="company_domain"
                value={formData.company_domain}
                onChange={handleChange}
                placeholder="https://acme.com"
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
                placeholder="Min. 8 characters"
                required
              />
            </div>
          </div>

          <div className="form-field">
            <label htmlFor="password_confirmation">Confirm Password</label>
            <div className="input-wrapper">
              <img src={lockIcon} alt="Confirm Password" className="input-icon" />
              <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                value={formData.password_confirmation}
                onChange={handleChange}
                placeholder="Confirm password"
                required
              />
            </div>
          </div>

          <div className="form-field">
            <label htmlFor="role">Role</label>
            <div className="select-wrapper">
              <select
                id="role"
                name="role"
                value={formData.role}
                onChange={handleChange}
                required
              >
                <option value="employee">Employee</option>
                <option value="manager">Manager</option>
                <option value="hr">HR</option>
              </select>
            </div>
          </div>
          
          <button type="submit" className="sign-up-button" onClick={handleSubmit}>
            Create Account
          </button>
          
          <div className="terms-policy">
            <p>By signing up, you agree to our <Link to="/terms">Terms</Link> and <Link to="/privacy">Privacy Policy</Link></p>
          </div>
          
          {/* Mobile-only login link */}
          <div className="mobile-login-account">
            <Link to="/login">Already have an account</Link>
          </div>
        </div>
      </div>

      <div className="signup-illustration">
        <img src={illustration} alt="Signup illustration" />
      </div>
    </div>
  );
};

export default Signup;