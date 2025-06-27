import React from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import styles from './DeliveryInfoSection.module.scss';

const DeliveryInfoSection = ({ onInfoChange }) => {
    return (
        <div className={styles.deliverySection}>
            <div className={styles.sectionHeader}>
                <span className={styles.icon}>🚚</span>
                <h3>Informations de livraison</h3>
            </div>

            <div className={styles.deliveryForm}>
                <div className={styles.formRow}>
                    <div className={styles.formColumn}>
                        <label>Date de livraison souhaitée</label>
                        <input
                            type="date"
                            className={styles.dateInput}
                            onChange={(e) => onInfoChange('deliveryDate', e.target.value)}
                        />
                    </div>

                    <div className={styles.formColumn}>
                        <label>Créneau horaire préféré</label>
                        <select
                            className={styles.timeSlotSelect}
                            onChange={(e) => onInfoChange('timeSlot', e.target.value)}
                        >
                            <option value="morning">Matin (8h - 12h)</option>
                            <option value="afternoon">Après-midi (14h - 18h)</option>
                        </select>
                    </div>
                </div>

                <div className={styles.formRow}>
                    <div className={styles.fullWidth}>
                        <label>Adresse de livraison</label>
                        <select
                            className={styles.addressSelect}
                            onChange={(e) => onInfoChange('address', e.target.value)}
                        >
                            <option value="bakery1">Boulangerie Durand - 12 rue de la Paix, 75001 Paris</option>
                        </select>
                    </div>
                </div>

                <div className={styles.formRow}>
                    <div className={styles.fullWidth}>
                        <label>Instructions spéciales</label>
                        <textarea
                            className={styles.instructionsTextarea}
                            placeholder="Précisez toute instruction particulière pour la livraison..."
                            onChange={(e) => onInfoChange('instructions', e.target.value)}
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DeliveryInfoSection;