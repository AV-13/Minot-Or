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
        setError('');
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
            logout();
            navigate('/');
        } catch (err) {
            console.error('Erreur lors de la suppression du compte:', err);
            setError('La suppression du compte a échoué. Veuillez réessayer.');
            setIsDeleting(false);
        }
    };

    return (
        <div className={styles.dangerZone}>
            <div className={styles.dangerHeader}>
                <div className={styles.warningIcon}>
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                    </svg>
                </div>
                <div className={styles.dangerText}>
                    <h3>Zone de danger</h3>
                    <p>Actions irréversibles concernant votre compte</p>
                </div>
            </div>

            <div className={styles.deleteSection}>
                <div className={styles.deleteInfo}>
                    <h4>Supprimer définitivement mon compte</h4>
                    <p>Cette action supprimera définitivement votre compte et toutes vos données associées. Cette action ne peut pas être annulée.</p>
                </div>

                {!showConfirmation ? (
                    <Button
                        onClick={handleDeleteRequest}
                        className={styles.deleteButton}
                    >
                        <svg className={styles.buttonIcon} viewBox="0 0 24 24" fill="none">
                            <path d="M3 6h18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                            <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                        Supprimer mon compte
                    </Button>
                ) : (
                    <div className={styles.confirmationModal}>
                        <div className={styles.confirmationContent}>
                            <div className={styles.confirmationHeader}>
                                <div className={styles.confirmationIcon}>
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2"/>
                                        <path d="M9 9l6 6" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M15 9l-6 6" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                </div>
                                <h4>Confirmer la suppression</h4>
                            </div>

                            <p>Êtes-vous absolument sûr de vouloir supprimer votre compte ? Cette action est définitive et irréversible.</p>

                            {error && (
                                <div className={styles.errorAlert}>
                                    <svg className={styles.errorIcon} viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2"/>
                                        <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                    <span>{error}</span>
                                </div>
                            )}

                            <div className={styles.buttonGroup}>
                                <Button
                                    onClick={handleCancelDelete}
                                    className={styles.cancelButton}
                                    disabled={isDeleting}
                                >
                                    <svg className={styles.buttonIcon} viewBox="0 0 24 24" fill="none">
                                        <path d="M18 6L6 18" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        <path d="M6 6l12 12" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                    </svg>
                                    Annuler
                                </Button>

                                <Button
                                    onClick={handleConfirmDelete}
                                    className={styles.confirmButton}
                                    disabled={isDeleting}
                                >
                                    {isDeleting ? (
                                        <>
                                            <div className={styles.buttonSpinner}></div>
                                            Suppression en cours...
                                        </>
                                    ) : (
                                        <>
                                            <svg className={styles.buttonIcon} viewBox="0 0 24 24" fill="none">
                                                <path d="M9 12l2 2 4-4" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                            Confirmer la suppression
                                        </>
                                    )}
                                </Button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default DeleteAccountSection;