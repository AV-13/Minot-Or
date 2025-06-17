import { useState } from 'react';
import RegisterForm from '../organisms/RegisterForm';
import CompanyForm from '../organisms/CompanyForm';
import AuthLayout from "../templates/AuthLayout";
import apiClient from '../../utils/apiClient';

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
            console.log('final data :',finalData);
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
            <h1>Inscription</h1>
            {step === 1 && <RegisterForm onNext={handleNext} />}
            {step === 2 && <CompanyForm onFinish={handleFinish} onBack={handleBack} />}
        </AuthLayout>
    );
};

export default Register;
