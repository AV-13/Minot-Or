import styles from './Logo.module.scss';

const Logo = ({ customClass = '' }) => (
    <img src="/images/logo1_minotor.png" alt="Logo" className={styles[customClass]} />
);

export default Logo;
