import styles from './Input.module.css';

const Input = ({ type = 'text', placeholder, className, ...props }) => (
    <input className={`${styles.input} ${className || ''}`} type={type} placeholder={placeholder} {...props} />
);

export default Input;
