import React from 'react';
import Button from '../Button';
import './Pricing.css';

interface PricingCardProps {
    name: string;
    subtitle?: string;
    price: string;
    period: string;
    description: string;
    features: string[];
    isPopular: boolean;
    buttonText: string;
    buttonVariant: 'primary' | 'outline' | 'secondary';
}

const PricingCard: React.FC<PricingCardProps> = ({ 
    name, 
    subtitle,
    price, 
    period, 
    description, 
    features, 
    isPopular,
    buttonText,
    buttonVariant
}) => {
    return (
        <div className={`pricing-card ${isPopular ? 'pricing-popular' : ''}`}>
            {isPopular && <div className="popular-badge">Most Popular</div>}
            
            <div className="pricing-header">
                <h3 className="pricing-name">{name}</h3>
                {subtitle && <span className="pricing-subtitle">{subtitle}</span>}
                
                <div className="pricing-price">
                    <span className="pricing-currency">$</span>
                    <span className="pricing-amount">{price}</span>
                    <span className="pricing-period">/{period}</span>
                </div>
                
                <p className="pricing-description">{description}</p>
            </div>
            
            <ul className="pricing-features">
                {features.map((feature, index) => (
                    <li key={index} className="pricing-feature-item">
                        <span className="feature-check">✓</span>
                        {feature}
                    </li>
                ))}
            </ul>
            
            <div className="pricing-action">
                <Button 
                    variant={buttonVariant} 
                    className={`pricing-button ${isPopular ? 'pricing-button-popular' : ''}`}
                >
                    {buttonText}
                </Button>
            </div>
        </div>
    );
};

export default PricingCard;