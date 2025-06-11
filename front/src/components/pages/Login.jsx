import LoginForm from '../organisms/LoginForm';
import AuthLayout from "../templates/AuthLayout";

const Login = () => (
    <AuthLayout>
        <h1>Connexion</h1>
        <LoginForm />
    </AuthLayout>
);

export default Login;
