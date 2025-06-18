import { useState } from 'react';
import { useNavigate } from 'react-router';
import { useAuth } from '../../contexts/AuthContext';
import RegisterForm from '../organisms/RegisterForm';
import CompanyForm from '../organisms/CompanyForm';
import AuthLayout from "../templates/AuthLayout";
import apiClient from '../../utils/apiClient';

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
            <h1>Inscription</h1>
            {step === 1 && <RegisterForm onNext={handleNext} />}
            {step === 2 && <CompanyForm onFinish={handleFinish} onBack={handleBack} />}
        </AuthLayout>
    );
};

export default Register;
