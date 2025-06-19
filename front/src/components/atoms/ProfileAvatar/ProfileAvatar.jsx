import React, { useEffect, useState } from "react";
import styles from "./ProfileAvatar.module.scss";
import Modal from "../Modal/Modal";
import AvatarPicker from "../AvatarPicker/AvatarPicker";

const ProfileAvatar = ({ userName }) => {
    const [avatar, setAvatar] = useState('/avatars/3d_1-removebg-preview.png');
    const [color, setColor] = useState('#FF9800');
    const [open, setOpen] = useState(false);

    useEffect(() => {
        const data = localStorage.getItem('profileAvatar');
        if (data) {
            const { avatar, color } = JSON.parse(data);
            setAvatar(avatar);
            setColor(color);
        }
    }, []);

    // Fonction appelée à chaque changement dans AvatarPicker
    const handleChange = ({ avatar, color }) => {
        setAvatar(avatar);
        setColor(color);
        localStorage.setItem('profileAvatar', JSON.stringify({ avatar, color }));
    };

    return (
        <>
            <div className={styles.profileAvatar} onClick={() => setOpen(true)} style={{ cursor: 'pointer' }}>
                <img
                    className={styles.avatar}
                    src={avatar}
                    alt="Avatar"
                    style={{ backgroundColor: color }}
                />
                <span className={styles.userName}>{userName}</span>
            </div>
            <Modal open={open} onClose={() => setOpen(false)}>
                <AvatarPicker onChange={handleChange} onClose={() => setOpen(false)} />
            </Modal>
        </>
    );
};

export default ProfileAvatar;