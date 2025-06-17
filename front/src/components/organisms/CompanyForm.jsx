import { useState } from 'react';
import InputWithLabel from '../molecules/InputWithLabel';
import Button from '../atoms/Button';

const CompanyForm = ({ onFinish, onBack }) => {
    const [data, setData] = useState({ companySiret: '' });

    const handleChange = e => {
        setData({ ...data, [e.target.name]: e.target.value });
    };

    const handleSubmit = e => {
        e.preventDefault();
        onFinish(data);
    };

    return (
        <>
            <form onSubmit={handleSubmit}>
                <InputWithLabel label="Numéro de siret" id="companySiret" name="companySiret" value={data.companySiret} onChange={handleChange} />
                <Button type="submit">Finaliser l'inscription</Button>
            </form>
                <Button type="button" onClick={onBack}>retour</Button>
        </>
    );
};

export default CompanyForm;
