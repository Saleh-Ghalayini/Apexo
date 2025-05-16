import './Login.css';
import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import logo from '../../../assets/images/apexo_logo.svg';
import mailIcon from '../../../assets/images/mail_icon.png';
import lockIcon from '../../../assets/images/lock_icon.png';
import illustration from '../../../assets/images/illustration.png';
import { useAuth } from '../../../hooks/useAuth';

const Login: React.FC = () => {
  const navigate = useNavigate();
  const { login } = useAuth();
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    rememberMe: false,
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setFormData(prevState => ({
      ...prevState,
      [name]: type === 'checkbox' ? checked : value,
    }));
    // Clear error when user starts typing again
    if (error) setError(null);
  };  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      await login(formData.email, formData.password);
      
      // Redirect to dashboard after successful login
      navigate('/dashboard');
      
    } catch (err: unknown) {
      console.error('Login failed:', err);
      const error = err as { response?: { data?: { error?: string, errors?: { email?: string[] } } } };
      if (error.response?.data?.error) {
        setError(error.response.data.error);
      } else if (error.response?.data?.errors?.email) {
        setError(error.response.data.errors.email[0]);
      } else {
        setError('Login failed. Please check your credentials and try again.');
      }
    } finally {
      setLoading(false);
    }
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
            {error && (
            <div className="error-message">
              {error}
            </div>
          )}
          
          <button 
            type="submit" 
            className="sign-in-button" 
            onClick={handleSubmit}
            disabled={loading}
          >
            {loading ? 'Signing in...' : 'Sign in'}
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