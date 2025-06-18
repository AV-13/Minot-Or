import InputWithLabel from '../../molecules/InputWithLabel/InputWithLabel';
import Button from '../../atoms/Button/Button';
import styles from './LoginForm.module.scss';
import { useState } from 'react';

const LoginForm = () => {
    const [form, setForm] = useState({ email: '', password: '' });

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        // Appelle API à faire !!!! 
        console.log('Login:', form);
    };

    return (
        <form className={styles.loginFormContainer} onSubmit={handleSubmit}>
            <InputWithLabel
                label="Email"
                id="email"
                name="email"
                type="email"
                placeholder="exemple@mail.com"
                value={form.email}
                onChange={handleChange}
            />
            <InputWithLabel
                label="Mot de passe"
                id="password"
                name="password"
                type="password"
                placeholder="Votre mot de passe"
                value={form.password}
                onChange={handleChange}
            />
            <Button type="submit">Se connecter</Button>
        </form>
    );
};

export default LoginForm;
