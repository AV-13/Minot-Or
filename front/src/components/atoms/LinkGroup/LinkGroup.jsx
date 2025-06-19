import styles from './LinkGroup.module.scss';

export default function LinkGroup({ title, links }) {
    return (
        <div className={styles.linkGroup}>
            <h4>{title}</h4>
            {links.map((link) => <a key={link} href="#" className={styles.footerLink}>{link}</a>)}
        </div>
    );
}
