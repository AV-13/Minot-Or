import styles from './RegisterSuccess.module.scss';
import Button from "../../atoms/Button/Button";
import {useNavigate} from "react-router";

const RegisterSuccess = () => {
    const navigate = useNavigate();

    const handleGoHome = () => {
        navigate('/');
    };

    return (
        <div className={styles.registerSuccessContainer}>

            <h1 className={styles.pageTitle}>
            <span className={styles.successIcon}>
            <img alt="Check" src="/icons/check.svg"/>
            </span>
                Inscription réussie
            </h1>
            <p>Félicitations ! Votre compte a bien été créé.</p>
            <p>Notre équipe étudiera votre dossier pour vous permettre d'accéder aux fonctionnalités.</p>
            <Button onClick={handleGoHome}>Retour à l'accueil</Button>
        </div>
    );
};
export default RegisterSuccess;
