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
        <></>
    );
};

export default PricingCard;