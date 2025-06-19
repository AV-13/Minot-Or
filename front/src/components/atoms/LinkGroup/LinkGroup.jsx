import styles from './LinkGroup.module.scss';

export default function LinkGroup({ title, links }: { title: string; links: string[] }) {
    return (
        <div className="link-group">
            <h4>{title}</h4>
            {links.map((link) => <a key={link} href="#" className="footer-link">{link}</a>)}
        </div>
    );
}
