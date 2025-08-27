import React, { useState } from 'react';
import Button from "../../atoms/Button/Button";
import Select from "../../atoms/Select";
import { ROLES } from "../../../constants/roles";
import styles from './UserRow.module.scss';
import Modal from "../../atoms/Modal/Modal";

export default function UserRow({ user, onDelete, onRoleChange }) {
    const [showConfirmation, setShowConfirmation] = useState(false);

    const handleDeleteClick = () => {
        setShowConfirmation(true);
    };

    const confirmDelete = () => {
        onDelete(user.id);
        setShowConfirmation(false);
    };

    const cancelDelete = () => {
        setShowConfirmation(false);
    };

    return (
        <>
            <tr>
                <td className={styles.emailCell}>{user.email}</td>
                <td>{user.name}</td>
                <td>
                    <span className={styles.roleTag}>
                        {Object.values(user.role)}
                    </span>
                </td>
                <td>
                    <Select
                        className={styles.roleSelect}
                        options={ROLES}
                        value={Object.values(user.role)[0].replace(/^ROLE_/, '')}
                        onChange={e => onRoleChange(user.id, e.target.value)}
                    />
                </td>
                <td>
                    <Button
                        variant="danger"
                        className={styles.deleteButton}
                        onClick={handleDeleteClick}
                    >
                        Supprimer
                    </Button>
                </td>
            </tr>

            <Modal open={showConfirmation} onClose={cancelDelete}>
                <div className={styles.confirmationModal}>
                    <h3>Confirmer la suppression</h3>
                    <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{user.name}</strong> ?</p>
                    <div className={styles.modalActions}>
                        <Button variant="secondary" onClick={cancelDelete}>Annuler</Button>
                        <Button variant="danger" onClick={confirmDelete}>Supprimer</Button>
                    </div>
                </div>
            </Modal>
        </>
    );
}