import { sanitizeInput, validateInput } from '../../../utils/xssProtection';
import styles from './Input.module.scss';

const Input = ({
                   value,
                   onChange,
                   type = 'text',
                   enableXSSProtection = true,
                   validationType = null,
                   ...props
               }) => {
    const handleChange = (e) => {
        let newValue = e.target.value;

        if (enableXSSProtection) {
            newValue = sanitizeInput(newValue);
            if (validationType && !validateInput(newValue, validationType)) {
                return;
            }
        }

        onChange(newValue);
    };

    return (
        <input
            {...props}
            type={type}
            value={value}
            onChange={handleChange}
            className={styles.input}
        />
    );
};

export default Input;