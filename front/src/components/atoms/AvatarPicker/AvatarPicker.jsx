import React, { useState } from 'react';
import { Compact } from '@uiw/react-color';
import styles from './AvatarPicker.module.scss';
import Button from "../Button/Button";

const avatars = Array.from({ length: 17 }, (_, i) => `/avatars/loreleiNeutral-${i + 1}.svg`);
const pastelColors = [
    '#FFD1DC',
    '#B5EAD7',
    '#C7CEEA',
    '#FFFACD',
    '#FFDAC1',
    '#E2F0CB',
    '#B5D8FA',
    '#F3C6E8',
    '#F9F6C1',
    '#D4A5A5',
];

const AvatarPicker = ({ onChange, onClose }) => {
    const [selectedColor, setSelectedColor] = useState('#B5EAD7');
    const [selectedAvatar, setSelectedAvatar] = useState(avatars[0]);

    const handleValidate = () => {
        const data = { color: selectedColor, avatar: selectedAvatar };
        localStorage.setItem('profileAvatar', JSON.stringify(data));
        if (onChange) onChange(data);
        if (onClose) onClose();
    };

    return (
        <div className={styles.pickerContainer}>
            <div className={styles.modalHeader}>
                <div className={styles.headerIcon}>
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="5" stroke="currentColor" strokeWidth="2"/>
                        <path d="M20 21a8 8 0 0 0-16 0" stroke="currentColor" strokeWidth="2"/>
                    </svg>
                </div>
                <div className={styles.headerText}>
                    <h2>Personnalisation d'avatar</h2>
                    <p>Choisissez votre avatar et personnalisez sa couleur de fond</p>
                </div>
            </div>

            <div className={styles.topSection}>
                <div className={styles.previewColumn}>
                    <h3>Aperçu</h3>
                    <div className={styles.previewCard}>
                        <div className={styles.avatarPreview}>
                            <img
                                style={{ backgroundColor: selectedColor }}
                                className={styles.selectedAvatar}
                                src={selectedAvatar}
                                alt="Aperçu avatar"
                            />
                        </div>
                    </div>
                </div>

                <div className={styles.colorColumn}>
                    <div className={styles.colorSection}>
                        <h3>Couleur de fond</h3>
                        <div className={styles.colorPickerWrapper}>
                            <Compact
                                colors={pastelColors}
                                color={selectedColor}
                                onChange={color => setSelectedColor(color.hex)}
                            />
                        </div>
                    </div>
                    <div className={styles.actionSection}>
                        <Button
                            onClick={handleValidate}
                            className={styles.validateButton}
                        >
                            <svg className={styles.buttonIcon} viewBox="0 0 24 24" fill="none">
                                <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2"/>
                            </svg>
                            Valider mon choix
                        </Button>
                    </div>
                </div>
            </div>

            <div className={styles.avatarSection}>
                <h3>Choisir un avatar</h3>
                <div className={styles.avatarsGrid}>
                    {avatars.map(avatar => (
                        <button
                            key={avatar}
                            className={`${styles.avatarBtn} ${selectedAvatar === avatar ? styles.selected : ''}`}
                            onClick={() => setSelectedAvatar(avatar)}
                            aria-label="Choisir cet avatar"
                        >
                            <img src={avatar} alt="" />
                        </button>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default AvatarPicker;