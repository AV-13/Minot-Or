import SocialIcons from '../../atoms/SocialIcons/SocialIcons';
import ContactInfo from '../../atoms/ContactInfo/ContactInfo';
import LinkGroup from '../../atoms/LinkGroup/LinkGroup';
import styles from './FooterSection.module.scss';

export default function FooterSection() {
    return (
        <div className={styles.footerSection}>
            <div className={styles.column}>
                <img src="/images/logo_minotor_white.png" alt="Minot'Or" className={styles.logo} />
                <p className="desc">
                    Spécialiste de la distribution de produits de minoterie pour les professionnels de la boulangerie.
                </p>
                <SocialIcons />
            </div>

            <LinkGroup
                title="Services"
                links={["Approvisionnement", "Livraison", "Gestion des stocks", "Collecte des invendus", "Conseil personnalisé"]}
            />

            <LinkGroup
                title="Entreprise"
                links={["À propos", "Équipe", "Carrières", "Témoignages", "Blog"]}
            />

            <div className={styles.column}>
                <h4>Contact</h4>
                <ContactInfo />
            </div>
        </div>
    );
}
