import React from 'react';
import Button from '../../atoms/Button/Button';
import styles from './QuotationSummary.module.scss';

const QuotationSummary = ({ cart, subtotal, vat, shippingCost, total, onSubmitQuotation, onSaveQuotation }) => {
    return (
        <div className={styles.summarySection}>
            <div className={styles.sectionHeader}>
                <span className={styles.icon}>📋</span>
                <h3>Résumé de la demande</h3>
            </div>

            <div className={styles.productList}>
                {cart.map(product => (
                    <div key={product.id} className={styles.productSummary}>
                        <div className={styles.productName}>
                            {product.name} ({product.quantity} × {product.unit})
                        </div>
                        <div className={styles.productPrice}>{product.totalPrice} €</div>
                    </div>
                ))}
            </div>

            <div className={styles.summaryDetails}>
                <div className={styles.summaryRow}>
                    <span>Sous-total</span>
                    <span>{subtotal} €</span>
                </div>
                <div className={styles.summaryRow}>
                    <span>TVA (5.5%)</span>
                    <span>{vat} €</span>
                </div>
                <div className={styles.summaryRow}>
                    <span>Frais de livraison</span>
                    <span>{shippingCost === 0 ? 'Gratuit' : `${shippingCost} €`}</span>
                </div>

                <div className={styles.totalRow}>
                    <span>Total estimé</span>
                    <span className={styles.totalPrice}>{total} €</span>
                </div>
            </div>

            <div className={styles.saveOption}>
                <p>Souhaitez-vous enregistrer cette demande dans vos favoris avant de la soumettre ?</p>
                <div className={styles.saveActions}>
                    <Button onClick={onSaveQuotation} customClass={styles.saveButton}>Oui, enregistrer</Button>
                    <Button onClick={() => {}} customClass={styles.cancelButton}>Non, merci</Button>
                </div>
            </div>

            <Button onClick={onSubmitQuotation} customClass={styles.submitButton}>
                <span className={styles.submitIcon}>📤</span> Soumettre la demande de devis
            </Button>

            <p className={styles.disclaimer}>
                En soumettant cette demande, vous acceptez nos <a href="#">conditions d'utilisation</a> et <a href="#">politique de confidentialité</a>
            </p>
        </div>
    );
};

export default QuotationSummary;