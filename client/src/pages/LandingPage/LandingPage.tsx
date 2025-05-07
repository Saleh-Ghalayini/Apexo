import Header from '../../components/Header';
import Hero from '../../components/Hero';
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