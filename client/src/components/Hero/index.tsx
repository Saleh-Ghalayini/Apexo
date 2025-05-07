import apexo_network from '../../assets/images/Apexo-network.svg';
import Button from '../Button';
import React from 'react';
import './Hero.css';

const Hero: React.FC = () => {

    return (
        <div className='hero'>
            <div className='description'>
                <div className='product-name'>Apexo</div>
                <div className='slogan'>Elevae Every Action</div>
                <p className='text'>
                    Your AI-powered assistant for streamlined meetings,<br />
                    effortless task management, intelligent workflow automation,<br />
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
                <img src={apexo_network} alt='network' className='network' />
            </div>
        </div>
    );
}

export default Hero;