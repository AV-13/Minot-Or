import React, { useState } from 'react';
import apiClient from '../../../utils/apiClient';
import InputWithLabel from "../../molecules/InputWithLabel/InputWithLabel";
import Button from "../../atoms/Button/Button";
import styles from './CompanyForm.module.scss';

const CompanyForm = ({ onFinish, onBack }) => {
    const [siret, setSiret] = useState('');
    const [companyData, setCompanyData] = useState(null);
    const [error, setError] = useState('');
    const [verified, setVerified] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    // Vérifie le numéro de Siret au clic sur le bouton "Vérifier"
    const handleVerify = async () => {
        setIsLoading(true)
        if (siret) {
            try {
                const response = await apiClient.get(`/companies/siret/${siret}`);
                // Si la company est trouvée, on soumet directement
                if (response) {
                    setCompanyData(response);
                    setError('');
                    onFinish({ companyId: response.id });
                }
            } catch (err) {
                if (err.response && err.response.status === 404) {
                    // Company non trouvée : affichage des champs supplémentaires
                    setCompanyData(null);
                    setError('Aucune company trouvée. Veuillez renseigner les informations.');
                    setVerified(true);
                } else {
                    setError('Votre entreprise n\'a pas pu être trouvée. Veuillez renseigner le formulaire ci-dessous. Votre demande sera traitée manuellement par notre équipe.');
                    setVerified(true);
                }
            }
        }
    };

    // Soumet le formulaire final avec les informations manuelles si nécessaire
    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            if (companyData) {
                onFinish({ companyId: companyData.id });
            } else {
                const companyData = {
                    companyName: e.target.companyName.value,
                    companyContact: e.target.companyContact.value,
                    companySiret: siret,
                };
                const response = await apiClient.post('/companies', companyData);

                onFinish({ companyId: response.id });
            }
        } catch (err) {
            setError('Erreur lors de la création de l\'entreprise. Veuillez réessayer.');
            console.error('Erreur lors de la création de l\'entreprise :', err);
        }
    };

    return (
        <form onSubmit={handleSubmit}>
            <a className={styles.backButton} onClick={onBack}><img alt="Retour" src="/icons/arrow-left.svg"/></a>
            <InputWithLabel
                label="Numéro de Siret"
                id="siret"
                type="text"
                placeholder="Exemple: 90362639800015"
                value={siret}
                onChange={(e) => setSiret(e.target.value)}
                required
            />
            <div className={styles.buttonsGroup}>
                <Button onClick={handleVerify}>Vérifier</Button>
            </div>
            {verified && error && <p className={styles.labelError}>{error}</p>}
            {verified && !companyData && (
                <div>
                    <InputWithLabel
                        label="Nom"
                        id="companyName"
                        name="companyName"
                        type="text"
                        placeholder="Nom de l'entreprise"
                        required
                    />
                    <InputWithLabel
                        label="Contact"
                        id="companyContact"
                        name="companyContact"
                        placeholder="Numéro de téléphone ou email"
                        type="text"
                        required
                    />
                    <Button type="submit" isLoading={isLoading}>Créer mon entreprise</Button>
                </div>
            )}
        </form>
    );
};

export default CompanyForm;
