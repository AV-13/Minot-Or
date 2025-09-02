import React from 'react';
import styles from './Modal.module.scss';
import { useEffect } from 'react';

const Modal = ({ open, onClose, children }) => {
    useEffect(() => {
        if (open) {
            // Bloque le scroll du body quand la modale est ouverte
            const originalOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';

            // Gestionnaire pour fermer avec Escape
            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    onClose();
                }
            };

            document.addEventListener('keydown', handleEscape);

            return () => {
                document.body.style.overflow = originalOverflow;
                document.removeEventListener('keydown', handleEscape);
            };
        }
    }, [open, onClose]);

    if (!open) return null;

    return (
        <div className={styles.overlay} onClick={onClose}>
            <div className={styles.modal} onClick={e => e.stopPropagation()}>
                <button
                    className={styles.close}
                    onClick={onClose}
                    aria-label="Fermer la modale"
                >
                    ×
                </button>
                {children}
            </div>
        </div>
    );
};

export default Modal;