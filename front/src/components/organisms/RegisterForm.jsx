import { useState } from 'react';
import InputWithLabel from '../molecules/InputWithLabel';
import Button from '../atoms/Button/Button';
import Error from "../atoms/Error";

const RegisterForm = ({ onNext }) => {
    const [data, setData] = useState({ email: '', password: '', firstName: '', lastName: '' });
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
            <form onSubmit={handleSubmit}>
                <InputWithLabel label="Prénom" id="firstName" name="firstName" value={data.firstName} onChange={handleChange} />
                <InputWithLabel label="Nom" id="lastName" name="lastName" value={data.lastName} onChange={handleChange} />
                <InputWithLabel label="Email" id="email" name="email" value={data.email} onChange={handleChange} />
                <InputWithLabel label="Mot de passe" id="password" name="password" type="password" value={data.password} onChange={handleChange} />
                <Button type="submit">Suivant</Button>
            </form>
        </>
    );
};

export default RegisterForm;
