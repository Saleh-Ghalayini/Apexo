import './Signup.css';
import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import logo from '../../../assets/images/apexo_logo.svg';
import mailIcon from '../../../assets/images/mail_icon.png';
import lockIcon from '../../../assets/images/lock_icon.png';
import userIcon from '../../../assets/images/user_icon.png';
import companyIcon from '../../../assets/images/company_icon.png';
import illustration from '../../../assets/images/illustration.png';
import { useAuth } from '../../../hooks/useAuth';

const Signup: React.FC = () => {
  const navigate = useNavigate();
  const { register } = useAuth();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company_name: '',
    company_domain: '',
    role: 'employee' as 'employee' | 'manager' | 'hr',
    job_title: '',
    department: '',
    phone: '',
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [validationErrors, setValidationErrors] = useState<Record<string, string>>({});

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prevState => ({
      ...prevState,
      [name]: value,
    }));
    
    // Clear specific field error when the field is modified
    if (validationErrors[name]) {
      setValidationErrors(prevErrors => {
        const newErrors = { ...prevErrors };
        delete newErrors[name];
        return newErrors;
      });
    }
    
    // Clear general error message
    if (error) setError(null);
  };

  const validate = () => {
    const errors: Record<string, string> = {};
    if (!formData.name.trim()) {
      errors.name = 'Full name is required.';
    }
    if (!formData.email.trim()) {
      errors.email = 'Email is required.';
    } else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(formData.email)) {
        errors.email = 'Please enter a valid email address.';
      }
    }
    if (!formData.company_name.trim()) {
      errors.company_name = 'Company name is required.';
    }
    if (!formData.company_domain.trim()) {
      errors.company_domain = 'Company domain is required.';
    }
    if (!formData.password) {
      errors.password = 'Password is required.';
    } else if (formData.password.length < 8) {
      errors.password = 'Password must be at least 8 characters.';
    }
    if (!formData.password_confirmation) {
      errors.password_confirmation = 'Please confirm your password.';
    } else if (formData.password !== formData.password_confirmation) {
      errors.password_confirmation = 'Passwords do not match.';
    }
    setValidationErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setValidationErrors({});
    if (!validate()) return;
    setLoading(true);
    try {
      await register(formData);
      // Redirect to dashboard after successful registration
      navigate('/dashboard');
    } catch (err: unknown) {
      console.error('Registration failed:', err);
      const error = err as { response?: { data?: { error?: string, errors?: Record<string, string[]> } } };
      
      if (error.response?.data?.errors) {
        // Handle validation errors
        const fieldErrors: Record<string, string> = {};
        Object.entries(error.response.data.errors).forEach(([field, messages]) => {
          fieldErrors[field] = Array.isArray(messages) ? messages[0] : messages as unknown as string;
        });
        setValidationErrors(fieldErrors);
      } else if (error.response?.data?.error) {
        // Handle general error
        setError(error.response.data.error);
      } else {
        // Handle unknown errors
        setError('Registration failed. Please try again later.');
      }
    } finally {
      setLoading(false);
    }
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
          <div className="form-row">
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
                  className={validationErrors.name ? 'input-error' : ''}
                />
              </div>
              {validationErrors.name && <div className="field-error">{validationErrors.name}</div>}
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
                  className={validationErrors.email ? 'input-error' : ''}
                />
              </div>
              {validationErrors.email && <div className="field-error">{validationErrors.email}</div>}
            </div>
          </div>

          <div className="form-row">
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
                  className={validationErrors.company_name ? 'input-error' : ''}
                />
              </div>
              {validationErrors.company_name && <div className="field-error">{validationErrors.company_name}</div>}
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
                  placeholder="acme.com"
                  required
                  className={validationErrors.company_domain ? 'input-error' : ''}
                />
              </div>
              {validationErrors.company_domain && <div className="field-error">{validationErrors.company_domain}</div>}
            </div>
          </div>

          <div className="form-row">
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
                  className={validationErrors.password ? 'input-error' : ''}
                />
              </div>
              {validationErrors.password && <div className="field-error">{validationErrors.password}</div>}
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
          </div>

          <div className="form-row">
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
            <div className="form-field">
              <label htmlFor="job_title">Job Title</label>
              <div className="input-wrapper">
                <img src={userIcon} alt="Job" className="input-icon" />
                <input
                  type="text"
                  id="job_title"
                  name="job_title"
                  value={formData.job_title}
                  onChange={handleChange}
                  placeholder="Software Engineer"
                />
              </div>
            </div>
          </div>

          <div className="form-row">
            <div className="form-field">
              <label htmlFor="department">Department</label>
              <div className="input-wrapper">
                <img src={companyIcon} alt="Department" className="input-icon" />
                <input
                  type="text"
                  id="department"
                  name="department"
                  value={formData.department}
                  onChange={handleChange}
                  placeholder="Engineering"
                />
              </div>
            </div>
            <div className="form-field">
              <label htmlFor="phone">Phone Number</label>
              <div className="input-wrapper">
                <img src={companyIcon} alt="Phone" className="input-icon" />
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                  placeholder="(123) 456-7890"
                />
              </div>
            </div>
          </div>
          
          {error && (
            <div className="error-message">
              {error}
            </div>
          )}
          
          <button 
            type="submit" 
            className="sign-up-button" 
            onClick={handleSubmit}
            disabled={loading}
          >
            {loading ? 'Creating Account...' : 'Create Account'}
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