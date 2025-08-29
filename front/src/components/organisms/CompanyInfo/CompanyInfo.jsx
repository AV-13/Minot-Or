import React, { useState, useEffect } from 'react';
import Card from '../../atoms/Card/Card';
import ContactInfo from '../../atoms/ContactInfo/ContactInfo';
import Button from '../../atoms/Button/Button';
import apiClient from '../../../utils/apiClient';
import styles from './CompanyInfo.module.scss';

const CompanyInfo = ({ companyId }) => {
    const [company, setCompany] = useState(null);
    const [loading, setLoading] = useState(true);
    const [updating, setUpdating] = useState(false);

    const fetchCompanyData = async () => {
        try {
            const data = await apiClient.get(`/companies/${companyId}`);
            setCompany(data);
        } catch (error) {
            console.error('Erreur lors de la récupération des données de l\'entreprise:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (!companyId) {
            setLoading(false);
            return;
        }
        fetchCompanyData();
    }, [companyId]);

    const handleUnsoldRecovered = async () => {
        if (!company || updating) return;
        setUpdating(true);
        try {
            await apiClient.patch(`/companies/${companyId}/unsold`, { unsold: false });
            setCompany(prev => ({ ...prev, unsold: false }));
        } catch (error) {
            console.error('Erreur lors de la mise à jour du statut des invendus:', error);
        } finally {
            setUpdating(false);
        }
    };

    const handleSignalUnsold = async () => {
        if (!company || updating) return;
        setUpdating(true);
        try {
            await apiClient.patch(`/companies/${companyId}/unsold`, { unsold: true });
            setCompany(prev => ({ ...prev, unsold: true }));
        } catch (error) {
            console.error('Erreur lors du signalement des invendus:', error);
        } finally {
            setUpdating(false);
        }
    };

    if (!companyId) return null;
    if (loading) return (
        <div className={styles.companySection}>
            <Card className={styles.companyCard}>
                <div className={styles.loadingState}>
                    <div className={styles.spinner}></div>
                    <p>Chargement des informations...</p>
                </div>
            </Card>
        </div>
    );
    if (!company) return (
        <div className={styles.companySection}>
            <Card className={styles.companyCard}>
                <div className={styles.emptyState}>
                    <svg className={styles.emptyIcon} viewBox="0 0 24 24" fill="none">
                        <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z" stroke="currentColor" strokeWidth="2"/>
                        <path d="M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4" stroke="currentColor" strokeWidth="2"/>
                    </svg>
                    <p>Aucune entreprise associée</p>
                </div>
            </Card>
        </div>
    );

    return (
        <div className={styles.companySection}>
            <Card className={styles.companyCard}>
                <div className={styles.cardHeader}>
                    <div className={styles.iconWrapper}>
                        <svg className={styles.icon} viewBox="0 0 24 24" fill="none">
                            <path d="M3 21h18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M5 21V7l8-4v18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M19 21V11l-6-4" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M9 9v.01" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M9 12v.01" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M9 15v.01" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                    </div>
                    <h2>Mon entreprise</h2>
                </div>

                <div className={styles.companyContent}>
                    <div className={styles.companyDetails}>
                        <ContactInfo
                            name={company.companyName}
                            email={company.companyContact}
                            phone=""
                            address=""
                        />

                        <div className={styles.siretSection}>
                            <div className={styles.siretCard}>
                                <span className={styles.siretLabel}>SIRET</span>
                                <span className={styles.siretValue}>{company.companySiret}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>

            <div className={styles.unsoldZone}>
                <div className={styles.unsoldHeader}>
                    <div className={styles.unsoldIcon}>
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z" stroke="currentColor" strokeWidth="2"/>
                            <path d="M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4" stroke="currentColor" strokeWidth="2"/>
                            <circle cx="12" cy="8" r="2" stroke="currentColor" strokeWidth="2"/>
                        </svg>
                    </div>
                    <div className={styles.unsoldText}>
                        <h3>Gestion des invendus</h3>
                        <p>Gérez le statut des produits invendus de votre entreprise</p>
                    </div>
                </div>

                <div className={styles.unsoldSection}>
                    {company.unsold ? (
                        <div className={`${styles.statusCard} ${styles.hasUnsold}`}>
                            <div className={styles.statusHeader}>
                                <div className={styles.statusIcon}>
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2"/>
                                        <path d="M12 6v6l4 2" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </div>
                                <div className={styles.statusText}>
                                    <h3>Invendus signalés</h3>
                                    <p>Des produits invendus sont disponibles pour récupération</p>
                                </div>
                            </div>
                            <Button
                                onClick={handleUnsoldRecovered}
                                disabled={updating}
                                variant="primary"
                                className={styles.actionButton}
                            >
                                {updating ? (
                                    <>
                                        <div className={styles.buttonSpinner}></div>
                                        Mise à jour...
                                    </>
                                ) : (
                                    <>
                                        <svg className={styles.buttonIcon} viewBox="0 0 24 24" fill="none">
                                            <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2"/>
                                        </svg>
                                        Marquer comme récupérés
                                    </>
                                )}
                            </Button>
                        </div>
                    ) : (
                        <div className={`${styles.statusCard} ${styles.noUnsold}`}>
                            <div className={styles.statusHeader}>
                                <div className={styles.statusIcon}>
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2"/>
                                    </svg>
                                </div>
                                <div className={styles.statusText}>
                                    <h3>Aucun invendu signalé</h3>
                                    <p>Avez-vous des invendus à signaler ?</p>
                                </div>
                            </div>
                            <Button
                                onClick={handleSignalUnsold}
                                disabled={updating}
                                variant="secondary"
                                className={styles.actionButton}
                            >
                                {updating ? (
                                    <>
                                        <div className={styles.buttonSpinner}></div>
                                        Mise à jour...
                                    </>
                                ) : (
                                    <>
                                        <svg className={styles.buttonIcon} viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2"/>
                                            <path d="M12 6v6l4 2" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>
                                        Signaler des invendus
                                    </>
                                )}
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default CompanyInfo;