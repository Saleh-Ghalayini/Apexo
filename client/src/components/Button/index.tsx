import React from 'react';
import './Button.css';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    children: React.ReactNode;
    variant?: 'primary' | 'secondary' | 'outline';
    size?: 'sm' | 'md' | 'lg';
    icon?: React.ReactNode;
    fullWidth?: boolean;
}

const Button: React.FC<ButtonProps> = ({ 
    children, 
    className = '', 
    variant = 'primary',
    size = 'md',
    icon,
    fullWidth = false,
    ...rest 
}) => {
    const buttonClasses = [
        'btn',
        `btn-${variant}`,
        size !== 'md' && `btn-${size}`,
        icon && 'btn-icon',
        fullWidth && 'btn-full',
        className
    ].filter(Boolean).join(' ');

    return(
        <button className={buttonClasses} {...rest}>
            {icon && <span className="btn-icon-wrapper">{icon}</span>}
            {children}
        </button>
    );
}

export default Button;