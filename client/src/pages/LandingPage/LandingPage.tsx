import ArrowDown from '../../components/ArrowDown';
import Header from '../../components/Header';
import Hero from '../../components/Hero';
import Features from '../../components/Features';
import Pricing from '../../components/Pricing';
import Footer from '../../components/Footer';
import './landingPage.css';
import React from 'react';

const LandingPage: React.FC = () => {

    return (
        <div className='landing-page'>
            <Header />
            <Hero />
            <ArrowDown />
            <Features />
            <Pricing />
            <Footer />
        </div>
    );
}

export default LandingPage;