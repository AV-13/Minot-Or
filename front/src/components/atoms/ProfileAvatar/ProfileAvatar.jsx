import React, { useEffect, useState } from "react";
import { NavLink } from 'react-router';
import styles from "./ProfileAvatar.module.scss";
import Modal from "../Modal/Modal";
import AvatarPicker from "../AvatarPicker/AvatarPicker";

const ProfileAvatar = ({ userName }) => {
    const [avatar, setAvatar] = useState('/avatars/loreleiNeutral-1.svg');
    const [color, setColor] = useState('#B5EAD7');
    const [open, setOpen] = useState(false);

    useEffect(() => {
        const data = localStorage.getItem('profileAvatar');
        if (data) {
            try {
                const { avatar, color } = JSON.parse(data);
                if (avatar) setAvatar(avatar);
                if (color) setColor(color);
            } catch (error) {
                console.error('Erreur lors du parsing des données avatar:', error);
            }
        }
    }, []);

    const handleAvatarChange = (data) => {
        setAvatar(data.avatar);
        setColor(data.color);
    };

    return (
        <>
            <div className={styles.profileAvatar}>
                <img
                    className={styles.avatar}
                    src={avatar}
                    alt="Avatar de profil"
                    style={{ backgroundColor: color, cursor: 'pointer' }}
                    onClick={() => setOpen(true)}
                />
                <NavLink to="/profile" className={styles.userName}>
                    {userName}
                </NavLink>
            </div>
            <Modal open={open} onClose={() => setOpen(false)}>
                <AvatarPicker
                    onChange={handleAvatarChange}
                    onClose={() => setOpen(false)}
                />
            </Modal>
        </>
    );
};

export default ProfileAvatar;