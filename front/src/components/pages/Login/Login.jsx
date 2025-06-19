import LoginForm from '../../organisms/LoginForm/LoginForm';
import AuthLayout from "../../templates/AuthLayout/AuthLayout";
import styles from './Login.module.scss';
import Header from "../../organisms/Header/Header";
import Footer from "../../organisms/Footer/Footer";

const Login = () => (
    <AuthLayout>
        <h1 className={styles.pageTitle}>Connexion</h1>
        <LoginForm />
    </AuthLayout>
);

export default Login;
