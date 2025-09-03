import styles from './Button.module.scss';
import Loader from '../Loader/Loader';

const Button = ({ children, isLoading, customClass = '', type = 'button', ...props }) => (
    <button
        className={`${styles.button} ${customClass}`}
        type={type}
        disabled={isLoading}
        {...props}
    >
        {isLoading ? <Loader /> : children}
    </button>
);

export default Button;