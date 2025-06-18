import { useState } from 'react';
import RegisterForm from '../../organisms/RegisterForm/RegisterForm';
import CompanyForm from '../../organisms/CompanyForm';
import AuthLayout from "../../templates/AuthLayout/AuthLayout";
import apiClient from '../../../utils/apiClient';
import styles from './Register.module.scss';
import {useNavigate} from "react-router";
import {useAuth} from "../../../contexts/AuthContext";

const Register = () => {
    const [step, setStep] = useState(1);
    const [formData, setFormData] = useState({});
    const navigate = useNavigate();
    const { login } = useAuth();

    const handleNext = (data) => {
        setFormData(prev => ({ ...prev, ...data }));
        setStep(step + 1);
    };

    const handleFinish = async (data) => {
        const finalData = { ...formData, ...data, role : "WaitingForValidation" };
        try {
            console.log('final data :',finalData);
            const result = await apiClient.post('/users', finalData);
            console.log('Utilisateur créé avec succès :', result);

            // Connexion automatique après inscription
            const credentials = {
                email: formData.email,
                password: formData.password
            };

            const authResponse = await apiClient.post('/login', credentials);
            login(authResponse.token);

            navigate('/');
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
