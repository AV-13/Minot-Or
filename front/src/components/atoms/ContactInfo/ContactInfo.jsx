import styles from './ContactInfo.module.scss';

export default function ContactInfo() {
    return (
        <div className="contact-info">
            <p><i className="icon-location" /> 123 Avenue de la Minoterie, Paris</p>
            <p><i className="icon-phone" /> +33 1 23 45 67 89</p>
            <p><i className="icon-mail" /> contact@minotor.fr</p>
        </div>
    );
}
