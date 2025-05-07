import Header from '../../components/Header';
import Hero from '../../components/Hero';
import './landingPage.css';
import React from 'react';

const LandingPage: React.FC = () => {

    return (
        <div className='landing-page'>
            <Header />
            <Hero />
        </div>
    );
}

export default LandingPage;