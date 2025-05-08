import React from 'react';
import './Features.css';
import FeatureCard from './FeatureCard';

const Features: React.FC = () => {
    const featuresData = [
        {
            id: 1,
            title: "Meeting Assistance",
            description: "Host, attend, and participate in meetings with AI that provides summaries, and extracts tasks for productive outcomes.",
            iconPlaceholder: "🤖"
        },
        {
            id: 2,
            title: "Task Management",
            description: "Get AI-powered reminders and track your progress seamlessly, with the ability to ask questions about your tasks.",
            iconPlaceholder: "📋"
        },
        {
            id: 3,
            title: "Automated Workflows",
            description: "Let AI handle the repetitive tasks, from sending reminders to generating reports, freeing up your time.",
            iconPlaceholder: "⚙️"
        },
        {
            id: 4,
            title: "Hiring Insights",
            description: "Gain deeper insights into candidates with AI analysis of interviews, including tone and soft skills.",
            iconPlaceholder: "👥"
        },
        {
            id: 5,
            title: "Slack Integration",
            description: "Receive instant updates, summaries, and task notifications directly within your slack channels.",
            iconPlaceholder: "💬"
        },
        {
            id: 6,
            title: "Email Communication",
            description: "Apexo communicates important information and reports directly to your inbox.",
            iconPlaceholder: "📧"
        },
        {
            id: 7,
            title: "Calendar Scheduling & Reminders",
            description: "Automate meeting scheduling and receive reminders to keep everyone on track.",
            iconPlaceholder: "📅"
        },
        {
            id: 8,
            title: "Centralized AI Chat",
            description: "Your direct line to Apexo for asking questions and initiating actions across your workflows.",
            iconPlaceholder: "💭"
        }
    ];

    return (
        <section id="features" className="features">
            
        </section>
    );
};

export default Features;