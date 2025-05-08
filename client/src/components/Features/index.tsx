import './Features.css';
import React from 'react';
import FeatureCard from './FeatureCard';
import chat from '../../assets/images/chat_icon.png';
import tasks from '../../assets/images/tasks_icon.png';
import slack from '../../assets/images/slack_icon.png';
import gmail from '../../assets/images/gmail_icon.png';
import hiring from '../../assets/images/hiring_icon.png';
import meeting from '../../assets/images/meeting_icon.png';
import schedule from '../../assets/images/schedule_icon.png';
import automation from '../../assets/images/automation_icon.png';

const Features: React.FC = () => {
    // Feature data for each card
    const featuresData = [
        {
            id: 1,
            title: "Meeting Assistance",
            description: "Host, attend, and participate in meetings with AI that provides summaries, and extracts tasks for productive outcomes.",
            iconPlaceholder: meeting
        },
        {
            id: 2,
            title: "Task Management",
            description: "Get AI-powered reminders and track your progress seamlessly, with the ability to ask questions about your tasks.",
            iconPlaceholder: tasks
        },
        {
            id: 3,
            title: "Automated Workflows",
            description: "Let AI handle the repetitive tasks, from sending reminders to generating reports, freeing up your time.",
            iconPlaceholder: automation
        },
        {
            id: 4,
            title: "Hiring Insights",
            description: "Gain deeper insights into candidates with AI analysis of interviews, including tone and soft skills.",
            iconPlaceholder: hiring
        },
        {
            id: 5,
            title: "Slack Integration",
            description: "Receive instant updates, summaries, and task notifications directly within your slack channels.",
            iconPlaceholder: slack
        },
        {
            id: 6,
            title: "Email Communication",
            description: "Apexo communicates important information and reports directly to your inbox.",
            iconPlaceholder: gmail
        },
        {
            id: 7,
            title: "Calendar Scheduling & Reminders",
            description: "Automate meeting scheduling and receive reminders to keep everyone on track.",
            iconPlaceholder: schedule
        },
        {
            id: 8,
            title: "Centralized AI Chat",
            description: "Your direct line to Apexo for asking questions and initiating actions across your workflows.",
            iconPlaceholder: chat
        }
    ];

    return (
        <section id="features" className="features">
            <div className="features-container">
                <h2 className="features-title">
                    <span className="title-regular">What</span> <span className="title-accent">Apexo</span> <span className="title-regular">Features</span>
                </h2>
                <p className="features-subtitle">
                    Explore The Key Capabilities That Make <span className="subtitle-accent">Apexo</span><br />
                    Your Indispensable AI-Powered Assistant.
                </p>
                
                <div className="feature-cards">
                    {featuresData.map((feature) => (
                        <FeatureCard 
                            key={feature.id}
                            title={feature.title}
                            description={feature.description}
                            iconPlaceholder={feature.iconPlaceholder}
                        />
                    ))}
                </div>
            </div>
        </section>
    );
};

export default Features;