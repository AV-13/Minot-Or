import { NavLink } from 'react-router-dom';

const Navbar = () => (
    <nav>
        <NavLink to="/">Accueil</NavLink> | <NavLink to="/login">Connexion</NavLink>
    </nav>
);

export default Navbar;
