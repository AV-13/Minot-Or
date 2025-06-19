import styles from './SocialIcons.module.scss';

export default function SocialIcons() {
    return (
        <div className={styles.socialIcons}>
            <span><img src="/icons/socials/facebook-f.svg"  alt="" /></span>
            <span><img src="/icons/socials/x-twitter.svg" alt=""/></span>
            <span><img src="/icons/socials/instagram.svg" alt=""/></span>
           <span><img src="/icons/socials/linkedin.svg" alt=""/></span>
        </div>
);
}
