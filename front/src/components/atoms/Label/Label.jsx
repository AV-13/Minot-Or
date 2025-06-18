import styles from './Label.module.css';

const Label = ({ children, htmlFor }) => (
    <label className={styles.label} htmlFor={htmlFor}>{children}</label>
);

export default Label;
