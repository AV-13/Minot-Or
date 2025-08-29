import React from 'react';
import { useAuth } from '../../../contexts/AuthContext';
import MainLayout from '../../templates/MainLayout';
import ProfileInfo from '../../organisms/ProfileInfo/ProfileInfo';
import CompanyInfo from '../../organisms/CompanyInfo/CompanyInfo';
import DeleteAccountSection from '../../organisms/DeleteAccountSection/DeleteAccountSection';
import styles from './Profile.module.scss';

const Profile = () => {
    const { user } = useAuth();
    let profilePicture = user?.profilePicture;
    let avatarBackgroundColor = '#6AAACF';

    if (!profilePicture) {
        const stored = localStorage.getItem('profileAvatar');
        if (stored) {
            try {
                const parsed = JSON.parse(stored);
                profilePicture = parsed.avatar;
                avatarBackgroundColor = parsed.color || '#6AAACF';
            } catch {}
        }
    }

    return (
        <MainLayout>
            <div className={styles.profileContainer}>
                <div className={styles.profileHeader}>
                    <div className={styles.headerBackground}></div>
                    <div className={styles.headerContent}>
                        <div className={styles.avatarSection}>
                            <div className={styles.avatarWrapper}>
                                <img
                                    src={profilePicture || '/default-avatar.svg'}
                                    alt="Avatar utilisateur"
                                    className={styles.avatar}
                                    style={{ backgroundColor: avatarBackgroundColor }}
                                />
                                <div className={styles.avatarBorder}></div>
                            </div>
                        </div>
                        <div className={styles.userTitle}>
                            <h1>{user?.username || 'Utilisateur'}</h1>
                            <span className={styles.userRole}>{user?.role || 'Utilisateur'}</span>
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
        </MainLayout>
    );
};

export default Profile;