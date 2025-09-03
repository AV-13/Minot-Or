import styles from './Input.module.scss';

const Input = ({
                   value,
                   onChange,
                   type = 'text',
                   enableXSSProtection = true,
                   ...props
               }) => {
    console.log('Input props:', { value, onChange: !!onChange, id: props.id }); // Debug

    return (
        <input
            {...props}
            type={type}
            value={value}
            onChange={onChange}
            className={styles.input}
        />
    );
};

export default Input;