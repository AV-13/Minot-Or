import React from 'react';
import Card from '../../atoms/Card/Card';
import ContactInfo from '../../atoms/ContactInfo/ContactInfo';
import styles from './ProfileInfo.module.scss';

const ProfileInfo = ({ user }) => {
    if (!user) return null;

    return (
        <Card className={styles.profileInfoCard}>
            <div className={styles.cardHeader}>
                <div className={styles.iconWrapper}>
                    <img src="/icons/user.svg" alt="Avatar" className={styles.icon} />
                </div>
                <h2>Informations personnelles</h2>
            </div>

            <div className={styles.infoContent}>
                <ContactInfo
                    name={user.username}
                    email={user.email}
                    phone=""
                    address=""
                />

                <div className={styles.roleSection}>
                    <div className={styles.roleCard}>
                        <span className={styles.roleLabel}>Rôle</span>
                        <span className={styles.roleValue}>{user.role || 'Utilisateur'}</span>
                    </div>
                </div>
            </div>
        </Card>
    );
};

export default ProfileInfo;