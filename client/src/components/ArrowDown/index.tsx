import arrow from '../../assets/images/down-arrow.png';
import Button from '../Button';
import './ArrowDown.css';
import React from 'react';

const ArrowDown: React.FC = () => {

    return (
        <a className='arrow'>
            <Button className='arrow-btn'>
                <img src={arrow} />
            </Button>

        </a>
    );
}

export default ArrowDown;