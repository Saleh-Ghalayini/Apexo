import React from 'react';
import './Button.css';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    children: React.ReactNode;
  }

const Button: React.FC<ButtonProps> = ({ children, className = '', ...rest }) => {
    return(
        <button className={`btn ${className}`} {...rest}>
            {children}
        </button>
    );
}

export default Button;