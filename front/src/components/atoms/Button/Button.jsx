import styles from './Button.module.scss';

const Button = ({ children, customClass = '', ...props }) => (
    <button className={`${styles.button} ${customClass}`} {...props}>{children}</button>
);

export default Button;
