import styles from './Input.module.css';

const Input = ({ type = 'text', placeholder, ...props }) => (
    <input className={styles.input} type={type} placeholder={placeholder} {...props} />
);

export default Input;
