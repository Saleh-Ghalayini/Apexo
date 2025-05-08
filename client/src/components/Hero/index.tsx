import apexo_network from '../../assets/images/Apexo-network.svg';
import Button from '../Button';
import React from 'react';
import './Hero.css';

const Hero: React.FC = () => {
    
    return (
        <section className='hero' id="about">
            <div className='description'>
                <div className='product-name-container'>
                    <div className='product-name'>Apexo</div>
                </div>
                <h2 className='slogan'>Elevate Every Action</h2>
                <p className='text'>
                    Your AI-powered assistant for streamlined meetings,
                    effortless task management, intelligent workflow automation,
                    and smarter hiring - all in one platform.
                </p>
                <p className='teams'>
                    Trusted by forward-thinking teams
                </p>
                <div className='btns'>
                    <Button className='btn start-btn'>Get Started</Button>
                    <Button className='btn try-btn'>Try Apexo Now</Button>
                </div>
            </div>
            <div className='structure'>
                <img 
                    src={apexo_network} 
                    alt='Apexo network visualization' 
                    className='network' 
                    loading="eager"
                />
            </div>
        </section>
    );
}

export default Hero;