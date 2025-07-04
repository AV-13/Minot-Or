import Label from '../../atoms/Label/Label';
import Input from '../../atoms/Input/Input';
import styles from './InputWithLabel.module.scss';

const InputWithLabel = ({ label, id, type, placeholder, className, ...props }) => (
    <div className={`${styles.inputLabelContainer} ${className || ''}`}>
        <Label htmlFor={id}>{label}</Label>
        <Input id={id} placeholder={placeholder} type={type} {...props} />
    </div>
);

export default InputWithLabel;
