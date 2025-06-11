import Label from '../atoms/Label';
import Input from '../atoms/Input';

const InputWithLabel = ({ label, id, type, ...props }) => (
    <div>
        <Label htmlFor={id}>{label}</Label>
        <Input id={id} type={type} {...props} />
    </div>
);

export default InputWithLabel;
