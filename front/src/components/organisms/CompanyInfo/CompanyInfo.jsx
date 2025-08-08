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

    // Fonction pour récupérer les informations de l'entreprise
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

    // Fonction pour marquer les invendus comme récupérés
    const handleUnsoldRecovered = async () => {
        if (!company || updating) return;

        setUpdating(true);
        try {
            await apiClient.patch(`/companies/${companyId}/unsold`, {
                unsold: false
            });
            setCompany(prev => ({
                ...prev,
                unsold: false
            }));
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
            // Utilisation d'apiClient pour appeler la route PATCH /api/companies/{id}/unsold
            await apiClient.patch(`/companies/${companyId}/unsold`, {
                unsold: true
            });
            setCompany(prev => ({
                ...prev,
                unsold: true
            }));
        } catch (error) {
            console.error('Erreur lors du signalement des invendus:', error);
        } finally {
            setUpdating(false);
        }
    };

    if (!companyId) return null;
    if (loading) return <Card><p>Chargement des informations de l'entreprise...</p></Card>;
    if (!company) return <Card><p>Aucune entreprise associée à votre compte.</p></Card>;

    return (
        <Card className={styles.companyCard}>
            <h2>Mon entreprise</h2>
            <div className={styles.companyInfo}>
                <ContactInfo
                    name={company.companyName}
                    email={company.companyContact}
                    phone=""
                    address=""
                />
                <div className={styles.additionalInfo}>
                    <p><strong>SIRET :</strong> {company.companySiret}</p>

                    {/* Affichage du statut des invendus */}
                    {company.unsold && (
                        <div className={styles.unsoldAlert}>
                            <p><strong>Invendus signalés !</strong> Des produits invendus sont disponibles.</p>
                            <Button
                                onClick={handleUnsoldRecovered}
                                disabled={updating}
                                variant="primary"
                            >
                                {updating ? 'Mise à jour...' : 'Marquer comme récupérés'}
                            </Button>
                        </div>
                    )}
                    {!company.unsold && (
                        <div className={styles.unsoldAlert}>
                            <p><strong>Avez vous des invendus à signaler ?</strong></p>
                            <Button
                                onClick={handleSignalUnsold}
                                disabled={updating}
                                variant="primary"
                            >
                                {updating ? 'Mise à jour...' : 'Signaler des invendus disponibles'}
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </Card>
    );
};

export default CompanyInfo;