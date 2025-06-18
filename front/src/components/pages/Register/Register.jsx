import { useState } from 'react';
import RegisterForm from '../../organisms/RegisterForm/RegisterForm';
import CompanyForm from '../../organisms/CompanyForm';
import AuthLayout from "../../templates/AuthLayout/AuthLayout";
import apiClient from '../../../utils/apiClient';
import styles from './Register.module.scss';

const Register = () => {
    const [step, setStep] = useState(1);
    const [formData, setFormData] = useState({});

    const handleNext = (data) => {
        setFormData(prev => ({ ...prev, ...data }));
        setStep(step + 1);
    };

    const handleFinish = async (data) => {
        const finalData = { ...formData, ...data };
        try {
            console.log(finalData);
            let testData = {
                email: "test",
                password: "test",
                firstName: "test",
                lastName: "test",
                role: "Baker",
                companyId: 1
            }
            const result = await apiClient.post('/users', testData);
            console.log('Utilisateur créé avec succès :', result);
        } catch (err) {
            console.error('Erreur lors de la création de \'utilisateur :', err.message);
        }
    };

    const handleBack = () => {
        setStep(step - 1);
    };

    return (
        <AuthLayout>
            <div className={styles.stepper}>
                <div className={styles.step}>
                    <div className={`${styles.circle} ${styles.active}`}>1</div>
                    <div className={styles.label}>Informations</div>
                </div>
                <div className={`${styles.line} ${styles.active}`}></div>
                <div className={styles.step}>
                    <div className={styles.circle}>2</div>
                    <div className={styles.label}>Entreprise</div>
                </div>
                <div className={styles.line}></div>
                <div className={styles.step}>
                    <div className={styles.circle}>3</div>
                    <div className={styles.label}>Vérification</div>
                </div>
            </div>
            <div className={styles.registerContainer}>
                <h1 className={styles.pageTitle}>Inscription</h1>
                <p className={styles.registerInformationLabel}>Commencez par renseigner vos informations personnelles
                    pour accéder à l'application.</p>
                {step === 1 && <RegisterForm onNext={handleNext}/>}
                {step === 2 && <CompanyForm onFinish={handleFinish} onBack={handleBack}/>}
            </div>
        </AuthLayout>
    );
};

export default Register;
