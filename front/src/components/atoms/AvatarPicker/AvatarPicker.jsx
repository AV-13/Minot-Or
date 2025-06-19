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
            <div className={styles.preview}>
                <div className={styles.avatarPreview}>
                    <div className={styles.colorPickerContainer} style={{marginTop: 18}}>
                        <Compact
                            colors={pastelColors}
                            color={selectedColor}
                            onChange={color => setSelectedColor(color.hex)}
                        />
                    </div>
                    <img
                        style={{backgroundColor: selectedColor}}
                        className={styles.selectedAvatar}
                        src={selectedAvatar}
                        alt="Aperçu avatar"
                    />
                </div>
            </div>
            <h2>Choisissez votre avatar</h2>
            <div className={styles.avatars}>
                {avatars.map(avatar => (
                    <button
                        key={avatar}
                        className={`${styles.avatarBtn} ${selectedAvatar === avatar ? styles.selected : ''}`}
                        onClick={() => setSelectedAvatar(avatar)}
                        aria-label="Choisir cet avatar"
                    >
                        <img src={avatar} alt=""/>
                    </button>
                ))}
            </div>
            <Button onClick={handleValidate}>Valider</Button>
        </div>
    );
};

export default AvatarPicker;