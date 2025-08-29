import styles from './Button.module.scss';
import Loader from '../Loader/Loader';

const Button = ({ children, isLoading, customClass = '', ...props }) => (
    <button className={`${styles.button} ${customClass}`} {...props}>
        {isLoading ? <Loader /> : children}
    </button>
);

export default Button;
