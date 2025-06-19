import styles from './FooterBottom.module.scss';

export default function FooterBottom() {
    return (
        <div className="footer-bottom">
            <p>© 2025 Minot'Or. Tous droits réservés.</p>
            <div className="legal-links">
                <a href="#">Politique de confidentialité</a>
                <a href="#">Conditions d'utilisation</a>
                <a href="#">Mentions légales</a>
            </div>
        </div>
    );
}
