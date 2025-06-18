import { useState } from 'react';
import apiClient from '../../utils/apiClient';

const CompanyForm = ({ onFinish, onBack }) => {
    const [siret, setSiret] = useState('');
    const [companyData, setCompanyData] = useState(null);
    const [error, setError] = useState('');
    const [verified, setVerified] = useState(false);

    // Vérifie le numéro de Siret au clic sur le bouton "Vérifier"
    const handleVerify = async () => {
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
                    setError('Erreur lors de la récupération de la company.');
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
            <label>
                Numéro de Siret:
                <input
                    type="text"
                    value={siret}
                    onChange={(e) => setSiret(e.target.value)}
                    required
                />
            </label>
            <button type="button" onClick={handleVerify}>
                Vérifier
            </button>
            {verified && error && <p>{error}</p>}
            {verified && !companyData && (
                <div>
                    <label>
                        Nom de la company:
                        <input type="text" name="companyName" required />
                    </label>
                    <label>
                        Contact de la company:
                        <input type="text" name="companyContact" required />
                    </label>
                    <button type="submit">Soumettre</button>
                </div>
            )}
            <button type="button" onClick={onBack}>
                Retour
            </button>
        </form>
    );
};

export default CompanyForm;