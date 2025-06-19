import FooterSection from '../../molecules/FooterSection/FooterSection';
import FooterBottom from '../../molecules/FooterBottom/FooterBottom';
import styles from './Footer.module.scss';

export default function Footer() {
    return (
        <footer className="site-footer">
            <FooterSection />
            <FooterBottom />
        </footer>
    );
}
