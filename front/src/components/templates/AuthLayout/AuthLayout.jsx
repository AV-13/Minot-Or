import styles from './AuthLayout.module.scss';
import Logo from "../../atoms/Logo/Logo";

const AuthLayout = ({ children }) => (
    <>

        <main className={styles.authContainer}>
            {children}
        </main>
    </>
);

export default AuthLayout;
