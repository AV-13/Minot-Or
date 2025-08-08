// front/src/components/pages/Profile/Profile.jsx
import React from 'react';
import { useAuth } from '../../../contexts/AuthContext';
import MainLayout from '../../templates/MainLayout';
import ProfileInfo from '../../organisms/ProfileInfo/ProfileInfo';
import CompanyInfo from '../../organisms/CompanyInfo/CompanyInfo';
import DeleteAccountSection from '../../organisms/DeleteAccountSection/DeleteAccountSection';
import styles from './Profile.module.scss';

const Profile = () => {
    const { user } = useAuth();

    return (
        <MainLayout>
            <div className={styles.profileContainer}>
                <h1>Mon Profil</h1>
                <div className={styles.profileSections}>
                    <ProfileInfo user={user} />
                    <CompanyInfo companyId={user?.companyId} />
                    <DeleteAccountSection />
                </div>
            </div>
        </MainLayout>
    );
};

export default Profile;