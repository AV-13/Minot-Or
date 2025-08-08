import React, { useState } from 'react';
import { useNavigate } from 'react-router';
import { useAuth } from '../../../contexts/AuthContext';
import Button from '../../atoms/Button/Button';
import apiClient from '../../../utils/apiClient';
import styles from './DeleteAccountSection.module.scss';

const DeleteAccountSection = () => {
    const [showConfirmation, setShowConfirmation] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const [error, setError] = useState('');
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    const handleDeleteRequest = () => {
        setShowConfirmation(true);
    };

    const handleCancelDelete = () => {
        setShowConfirmation(false);
    };

    const handleConfirmDelete = async () => {
        if (!user || !user.id) {
            setError('Informations utilisateur non disponibles');
            return;
        }

        setIsDeleting(true);
        setError('');

        try {
            await apiClient.delete(`/users/${user.id}`);

            // Déconnexion de l'utilisateur après suppression du compte
            logout();

            // Redirection vers la page d'accueil
            navigate('/');
        } catch (err) {
            console.error('Erreur lors de la suppression du compte:', err);
            setError('La suppression du compte a échoué. Veuillez réessayer.');
            setIsDeleting(false);
        }
    };

    return (
        <div className={styles.deleteSection}>
            <h3 className={styles.sectionTitle}>Supprimer mon compte</h3>
            <p>
                Cette action est irréversible et supprimera définitivement votre compte et toutes vos données.
            </p>

            {!showConfirmation ? (
                <Button
                    onClick={handleDeleteRequest}
                    className={styles.deleteButton}
                >
                    Supprimer mon compte
                </Button>
            ) : (
                <div className={styles.confirmationBox}>
                    <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>

                    <div className={styles.buttonGroup}>
                        <Button
                            onClick={handleCancelDelete}
                            className={styles.cancelButton}
                            disabled={isDeleting}
                        >
                            Annuler
                        </Button>

                        <Button
                            onClick={handleConfirmDelete}
                            className={styles.confirmButton}
                            disabled={isDeleting}
                        >
                            {isDeleting ? 'Suppression...' : 'Confirmer la suppression'}
                        </Button>
                    </div>

                    {error && <p className={styles.error}>{error}</p>}
                </div>
            )}
        </div>
    );
};

export default DeleteAccountSection;