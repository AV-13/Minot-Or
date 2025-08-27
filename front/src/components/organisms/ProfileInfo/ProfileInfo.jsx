import React from 'react';
import Card from '../../atoms/Card/Card';
import ContactInfo from '../../atoms/ContactInfo/ContactInfo';
import styles from './ProfileInfo.module.scss';

const ProfileInfo = ({ user }) => {
    if (!user) return null;

    return (
        <Card className={styles.profileInfoCard}>
            <h2>Mes informations</h2>
            <div className={styles.userInfo}>
                <ContactInfo
                    name={user.username}
                    email={user.email}
                    phone={user.phone}
                    address={user.address}
                />
                <div className={styles.roleInfo}>
                    <span className={styles.roleLabel}>Rôle :</span>
                    <span className={styles.roleValue}>{user.role}</span>
                </div>
            </div>
        </Card>
    );
};

export default ProfileInfo;