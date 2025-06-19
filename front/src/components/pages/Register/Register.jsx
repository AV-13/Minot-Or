import { useState } from 'react';
import RegisterForm from '../../organisms/RegisterForm/RegisterForm';
import CompanyForm from '../../organisms/CompanyForm/CompanyForm';
import AuthLayout from "../../templates/AuthLayout/AuthLayout";
import apiClient from '../../../utils/apiClient';
import styles from './Register.module.scss';
import {useNavigate} from "react-router";
import {useAuth} from "../../../contexts/AuthContext";
import RegisterSuccess from "../../organisms/RegisterSuccess/RegisterSuccess";
import Header from "../../organisms/Header/Header";
import Footer from "../../organisms/Footer/Footer";

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
            // Si l'utilisateur a été créé avec succès, on peut procéder à la connexion
            // Connexion automatique après inscription
            const credentials = {
                email: formData.email,
                password: formData.password
            };

            const authResponse = await apiClient.post('/login', credentials);
            login(authResponse.token);
            setStep(3);
            setTimeout(() => navigate('/'), 5000);
            // navigate('/');
        } catch (err) {
            console.error('Erreur lors de la création de \'utilisateur :', err.message);
        }
    };

    const handleBack = () => {
        setStep(step - 1);
    };

    return (
        <AuthLayout>
            <Header></Header>
            <div className={styles.stepper}>
                <div className={styles.step}>
                    <div className={`${styles.circle} ${step >= 1 ? styles.active : ''}`}>1</div>
                    <div className={styles.label}>Informations</div>
                </div>
                <div className={`${styles.line} ${step > 1 ? styles.active : ''}`}></div>
                <div className={styles.step}>
                    <div className={`${styles.circle} ${step >= 2 ? styles.active : ''}`}>2</div>
                    <div className={styles.label}>Entreprise</div>
                </div>
                <div className={`${styles.line} ${step > 2 ? styles.active : ''}`}></div>
                <div className={styles.step}>
                    <div className={`${styles.circle} ${step === 3 ? styles.active : ''}`}>3</div>
                    <div className={styles.label}>Vérification</div>
                </div>
            </div>
            <div className={styles.registerContainer}>
                {step === 1 && <RegisterForm onNext={handleNext}/>}
                {step === 2 && <CompanyForm onFinish={handleFinish} onBack={handleBack}/>}
                {step === 3 && <RegisterSuccess />}
            </div>
            <Footer></Footer>
        </AuthLayout>
    );
};

export default Register;
