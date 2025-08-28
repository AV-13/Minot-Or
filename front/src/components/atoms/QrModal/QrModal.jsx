// front/src/components/atoms/QrModal/QrModal.jsx
import React, { useRef } from 'react';
import styles from './QrModal.module.scss';
import Button from "../Button/Button";

export default function QrModal({ qrCode, open, onClose }) {
    const qrRef = useRef();

    const handlePrint = () => {
        const printWindow = window.open('', '', 'width=400,height=400');
        printWindow.document.write(`<img src="https://api.qrserver.com/v1/create-qr-code/?data=${qrCode}&size=200x200" />`);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    };

    if (!open) return null;

    return (
        <div className={styles.overlay}>
            <div className={styles.modal}>
                <h2>QR Code</h2>
                <img
                    ref={qrRef}
                    src={`https://api.qrserver.com/v1/create-qr-code/?data=${qrCode}&size=200x200`}
                    alt="QR Code"
                />
                <div className={styles.actions}>
                    <Button onClick={handlePrint}>Imprimer</Button>
                    <Button onClick={onClose}>Fermer</Button>
                </div>
            </div>
        </div>
    );
}