import Label from '../../atoms/Label/Label';
import Input from '../../atoms/Input/Input';
import styles from './InputWithLabel.module.scss';

const InputWithLabel = ({ label, id, type, placeholder, className, onChange, value, error, ...props }) => {
    console.log('InputWithLabel props:', { onChange, value, id }); // Debug

    return (
        <div className={`${styles.inputLabelContainer} ${className || ''}`}>
            <Label htmlFor={id}>{label}</Label>
            <Input
                id={id}
                placeholder={placeholder}
                type={type}
                onChange={onChange}
                value={value}
                {...props}
            />
            {error && <span className={styles.error}>{error}</span>}
        </div>
    );
};

export default InputWithLabel;