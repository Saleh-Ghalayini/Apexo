import ArrowDown from '../../components/ArrowDown';
import Header from '../../components/Header';
import Hero from '../../components/Hero';
import './landingPage.css';
import React from 'react';

const LandingPage: React.FC = () => {

    return (
        <div className='landing-page'>
            <Header />
            <Hero />
            <ArrowDown />
        </div>
    );
}

export default LandingPage;