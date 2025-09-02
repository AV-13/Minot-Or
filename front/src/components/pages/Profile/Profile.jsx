import React, { useState } from 'react';
import { useAuth } from '../../../contexts/AuthContext';
import MainLayout from '../../templates/MainLayout';
import ProfileInfo from '../../organisms/ProfileInfo/ProfileInfo';
import CompanyInfo from '../../organisms/CompanyInfo/CompanyInfo';
import DeleteAccountSection from '../../organisms/DeleteAccountSection/DeleteAccountSection';
import Modal from '../../atoms/Modal/Modal';
import AvatarPicker from '../../atoms/AvatarPicker/AvatarPicker';
import styles from './Profile.module.scss';

const Profile = () => {
    const { user } = useAuth();
    const [isAvatarModalOpen, setIsAvatarModalOpen] = useState(false);
    const [profilePicture, setProfilePicture] = useState(user?.profilePicture);
    const [avatarBackgroundColor, setAvatarBackgroundColor] = useState('#6AAACF');

    // Initialisation de l'avatar depuis localStorage
    React.useEffect(() => {
        if (!profilePicture) {
            const stored = localStorage.getItem('profileAvatar');
            if (stored) {
                try {
                    const parsed = JSON.parse(stored);
                    setProfilePicture(parsed.avatar);
                    setAvatarBackgroundColor(parsed.color || '#6AAACF');
                } catch {}
            }
        }
    }, [profilePicture]);

    const handleAvatarChange = (data) => {
        setProfilePicture(data.avatar);
        setAvatarBackgroundColor(data.color);
        setIsAvatarModalOpen(false);
    };

    const handleAvatarClick = () => {
        setIsAvatarModalOpen(true);
    };

    return (
        <MainLayout>
            <div className={styles.profileContainer}>
                <div className={styles.profileHeader}>
                    <div className={styles.headerBackground}></div>
                    <div className={styles.headerContent}>
                        <div className={styles.avatarSection}>
                            <div className={styles.avatarWrapper} onClick={handleAvatarClick}>
                                <div className={styles.avatarBorder}></div>
                                <img
                                    className={styles.avatar}
                                    src={profilePicture || '/avatars/loreleiNeutral-1.svg'}
                                    alt="Avatar de profil"
                                    style={{ backgroundColor: avatarBackgroundColor }}
                                />
                                <div className={styles.editOverlay}>
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div className={styles.userTitle}>
                            <h1>{user?.firstName} {user?.lastName}</h1>
                        </div>
                        <div className={styles.userRole}>
                            {user?.role === 'admin' ? 'Administrateur' : 'Utilisateur'}
                        </div>
                    </div>
                </div>

                <div className={styles.profileContent}>
                    <div className={styles.contentGrid}>
                        <div className={styles.leftColumn}>
                            <ProfileInfo user={user} />
                        </div>
                        <div className={styles.rightColumn}>
                            <CompanyInfo companyId={user?.companyId} />
                        </div>
                    </div>

                    <div className={styles.dangerZone}>
                        <DeleteAccountSection />
                    </div>
                </div>
            </div>

            <Modal open={isAvatarModalOpen} onClose={() => setIsAvatarModalOpen(false)}>
                <AvatarPicker
                    onChange={handleAvatarChange}
                    onClose={() => setIsAvatarModalOpen(false)}
                />
            </Modal>
        </MainLayout>
    );
};

export default Profile;