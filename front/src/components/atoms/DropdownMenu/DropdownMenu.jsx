// front/src/components/atoms/DrowdownMenu/DropdownMenu.jsx
import React, { useState, useRef, useEffect } from 'react';
import { NavLink } from 'react-router';
import styles from './DropdownMenu.module.scss';

const DropdownMenu = ({ trigger, items, mainLink }) => {
    const [isOpen, setIsOpen] = useState(false);
    const menuRef = useRef(null);

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (menuRef.current && !menuRef.current.contains(event.target)) {
                setIsOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    return (
        <div
            className={styles.dropdown}
            ref={menuRef}
            onMouseEnter={() => setIsOpen(true)}
            onMouseLeave={() => setIsOpen(false)}
        >
            <NavLink to={mainLink} className={({isActive}) => isActive ? styles.active : undefined}>
                {trigger}
            </NavLink>
            <span className={styles.arrow}>{isOpen ? '▲' : '▼'}</span>

            {isOpen && (
                <div className={styles.menu}>
                    {items.map((item, index) => (
                        <NavLink
                            key={index}
                            to={item.path}
                            className={({isActive}) => isActive ? styles.dropdownActive : undefined}
                        >
                            {item.label}
                        </NavLink>
                    ))}
                </div>
            )}
        </div>
    );
};

export default DropdownMenu;