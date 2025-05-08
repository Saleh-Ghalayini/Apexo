import React from 'react';
import './Features.css';

interface FeatureCardProps {
    title: string;
    description: string;
    iconPlaceholder: string | React.ReactNode; // Using ReactNode for image imports or JSX
}

const FeatureCard: React.FC<FeatureCardProps> = ({ title, description, iconPlaceholder }) => {
    
    // Checking if iconPlaceholder is a single emoji (string of length 1-2) or imported image
    const isEmoji = typeof iconPlaceholder === 'string' && iconPlaceholder.length <= 2;
    
    return (
        <div className="feature-card">
            <div className="feature-icon">
                {isEmoji ? (
                    iconPlaceholder
                ) : (
                    <img src={iconPlaceholder as string} alt={title} className="feature-image" />
                )}
            </div>
            <h3 className="feature-title">{title}</h3>
            <p className="feature-description">{description}</p>
        </div>
    );
};

export default FeatureCard;