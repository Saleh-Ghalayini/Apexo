import React from 'react';
 import './ArrowDown.css';
 import arrow from '../../assets/images/down-arrow.png';

const ArrowDown: React.FC = () => {

    // A function that scrolls from the About section to the Features section
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