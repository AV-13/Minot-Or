import { useState } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Button from '../../atoms/Button/Button';
import Error from "../../atoms/Error";
import styles from './RegisterForm.module.scss';
import apiClient from "../../../utils/apiClient";

const RegisterForm = ({ onNext }) => {
    const [data, setData] = useState({ email: '', password: '', firstName: '', lastName: '' });
    const [error, setError] = useState(null);

    const handleChange = e => {
        setData({ ...data, [e.target.name]: e.target.value });
    };

    const handleSubmit = async e => {
        e.preventDefault();
        setError(null);

        try {
            const res = await apiClient.get(`/users/verify?email=${encodeURIComponent(data.email)}`);
            if (res.exists === true) {
                setError("Cet email est déjà utilisé.");
                return;
            }
        } catch (err) {
            setError("Erreur lors de la vérification de l'email.");
            return;
        }

        onNext(data);
    };

    return (
        <>
            <Error children={error}/>
            <p className={styles.registerInformationLabel}>Commencez par renseigner vos informations personnelles
                pour accéder à l'application.</p>
            <form className={styles.registerFormContainer} onSubmit={handleSubmit}>
                <h2 className={styles.formTitle}><img src="/icons/user.svg"  alt="Utilisateur"/>Informations personnelles</h2>
                <InputWithLabel placeholder="Votre prénom" label="Prénom" id="firstName" name="firstName" value={data.firstName} onChange={handleChange} />
                <InputWithLabel placeholder="Votre nom" label="Nom" id="lastName" name="lastName" value={data.lastName} onChange={handleChange} />
                <InputWithLabel placeholder="exemple@email.com" label="Email" id="email" name="email" value={data.email} onChange={handleChange} />
                <InputWithLabel placeholder="Votre mot de passe" label="Mot de passe" id="password" name="password" type="password" value={data.password} onChange={handleChange} />
                <Button type="submit">Suivant</Button>
            </form>
        </>
    );
};

export default RegisterForm;
