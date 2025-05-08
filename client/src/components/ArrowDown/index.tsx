import './ArrowDown.css';
import React from 'react';
import arrow from '../../assets/images/down-arrow.png';

const ArrowDown: React.FC = () => {
    const scrollToNextSection = () => {
        const about_section = document.getElementById('about');
        const features_section = document.getElementById('features');
        
        if (about_section)
            if (features_section) {
                features_section.scrollIntoView({ behavior: 'smooth' });
            }
        else
            window.scrollBy({
                top: window.innerHeight,
                behavior: 'smooth'
            });
    };

    return (
        <div className='arrow'>
            <button 
                className='arrow-btn'
                onClick={scrollToNextSection}
                aria-label="Scroll to next section"
            >
                <img src={arrow} alt="Scroll down" />
            </button>
        </div>
    );
}

export default ArrowDown;