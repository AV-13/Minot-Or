import styles from './AuthLayout.module.scss';
import Logo from "../../atoms/Logo/Logo";
import Navbar from "../../organisms/Navbar/Navbar";

const AuthLayout = ({ children }) => (
    <>
        <Navbar />
        <main className={styles.authContainer}>
            {children}
        </main>
    </>
);

export default AuthLayout;
