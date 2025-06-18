import { useState } from 'react';
import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Button from '../../atoms/Button/Button';
import Error from "../../atoms/Error";
import styles from './RegisterForm.module.scss';

const RegisterForm = ({ onNext }) => {
    const [data, setData] = useState({ email: '', password: '', firstname: '', lastname: '' });
    const [error, setError] = useState(null);

    const handleChange = e => {
        setData({ ...data, [e.target.name]: e.target.value });
    };

    const handleSubmit = e => {
        e.preventDefault();
        onNext(data);
    };

    return (
        <>
            <Error children={error}/>
            <form className={styles.registerFormContainer} onSubmit={handleSubmit}>
                <h2 className={styles.formTitle}><img src="/icons/user.svg"  alt="Utilisateur"/>Informations personnelles</h2>
                <InputWithLabel placeholder="Votre prénom" label="Prénom" id="firstname" name="firstname" value={data.firstname} onChange={handleChange} />
                <InputWithLabel placeholder="Votre nom" label="Nom" id="lastname" name="lastname" value={data.lastname} onChange={handleChange} />
                <InputWithLabel placeholder="exemple@email.com" label="Email" id="email" name="email" value={data.email} onChange={handleChange} />
                <InputWithLabel placeholder="Votre mot de passe" label="Mot de passe" id="password" name="password" type="password" value={data.password} onChange={handleChange} />
                <Button type="submit">Suivant</Button>
            </form>
        </>
    );
};

export default RegisterForm;
