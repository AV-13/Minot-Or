import { useState } from 'react';
import RegisterForm from '../organisms/RegisterForm';
import CompanyForm from '../organisms/CompanyForm';
import AuthLayout from "../templates/AuthLayout";

const Register = () => {
    const [step, setStep] = useState(1);
    const [formData, setFormData] = useState({});

    const handleNext = (data) => {
        setFormData(prev => ({ ...prev, ...data }));
        setStep(step + 1);
    };

    const handleFinish = (data) => {
        const finalData = { ...formData, ...data };
        console.log('Inscription complète :', finalData);
        // Envoie les données à l'API ici
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
