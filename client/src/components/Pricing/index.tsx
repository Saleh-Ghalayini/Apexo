import React from 'react';
import './Pricing.css';
import PricingCard from './PricingCard';

const Pricing: React.FC = () => {
    
    // Pricing plans data
    const pricingPlans: {
        id: number;
        name: string;
        subtitle?: string;
        price: string;
        period: string;
        description: string;
        features: string[];
        isPopular: boolean;
        buttonText: string;
        buttonVariant: 'primary' | 'outline' | 'secondary';
    }[] = [
        {
            id: 1,
            name: 'Free',
            price: '0',
            period: 'forever',
            description: 'Perfect for trying out Apexo',
            features: [
                '3 AI-assisted meetings per month',
                '10 tasks with reminders',
                'Basic meeting summaries',
                'Email notifications',
                'Single user only'
            ],
            isPopular: false,
            buttonText: 'Get Started',
            buttonVariant: 'outline' as const
        },
        {
            id: 2,
            name: 'Pro',
            subtitle: 'Individual',
            price: '19',
            period: 'per month',
            description: 'Everything you need as an individual',
            features: [
                'Unlimited AI-assisted meetings',
                'Unlimited tasks with smart reminders',
                'Advanced meeting insights & summaries',
                'Email & Slack integrations',
                'Calendar scheduling',
                'AI hiring assistant (5 interviews/month)',
                'Priority support'
            ],
            isPopular: true,
            buttonText: 'Get Pro',
            buttonVariant: 'primary' as const
        },
        {
            id: 3,
            name: 'Pro',
            subtitle: 'Team',
            price: '49',
            period: 'per month',
            description: 'Ideal for teams up to 10 members',
            features: [
                'Everything in Pro Individual',
                'Up to 10 team members',
                'Team task assignments & tracking',
                'Collaborative meeting notes',
                'Advanced workflow automation',
                'Custom integrations',
                'AI hiring assistant (20 interviews/month)',
                'Dedicated account manager'
            ],
            isPopular: false,
            buttonText: 'Get Team Pro',
            buttonVariant: 'primary'
        }
    ];

    return (
        <section id="pricing" className="pricing">
            <div className="pricing-container">
                <h2 className="pricing-title">
                    <span className="title-regular">Simple</span> <span className="title-accent">Pricing</span>
                </h2>
                <p className="pricing-subtitle">
                    Choose the plan that fits your needs.<br />
                    All plans include a 14-day free trial.
                </p>
                
                <div className="pricing-cards">
                    {pricingPlans.map((plan) => (
                        <PricingCard 
                            key={plan.id}
                            name={plan.name}
                            subtitle={plan.subtitle}
                            price={plan.price}
                            period={plan.period}
                            description={plan.description}
                            features={plan.features}
                            isPopular={plan.isPopular}
                            buttonText={plan.buttonText}
                            buttonVariant={plan.buttonVariant}
                        />
                    ))}
                </div>
            </div>
        </section>
    );
};

export default Pricing;