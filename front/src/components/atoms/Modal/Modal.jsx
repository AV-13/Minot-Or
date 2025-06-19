import React from 'react';
import styles from './Modal.module.scss';
import { useEffect } from 'react';


const Modal = ({ open, onClose, children }) => {
    useEffect(() => {
        document.body.style.overflow = 'hidden';
        return () => { document.body.style.overflow = ''; };
    }, []);
    if (!open) return null;
    return (
        <div className={styles.overlay} onClick={onClose}>
            <div className={styles.modal} onClick={e => e.stopPropagation()}>
                <button className={styles.close} onClick={onClose}>×</button>
                {children}
            </div>
        </div>
    );
};

export default Modal;