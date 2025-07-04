import MainLayout from '../../templates/MainLayout';
import { useAuth } from '../../../contexts/AuthContext';
import Header from "../../organisms/Header/Header";
import OurServices from "../../organisms/OurServices/OurServices";
import Footer from "../../organisms/Footer/Footer";
import CatchyFooter from "../../organisms/CatchyFooter/CatchyFooter";

const Home = () => {
    const { user } = useAuth();

    return (
        <MainLayout>
            <h1>Bienvenue sur l'accueil</h1>
            <Header></Header>
            {user && (
                <p>
                    Votre rôle: {
                    user.roles && Object.values(user.roles).length > 0
                        ? Object.values(user.roles)[0]
                        : 'Aucun rôle'
                }
                </p>            )}
            <OurServices></OurServices>
            <CatchyFooter></CatchyFooter>
        </MainLayout>
    );
};

export default Home;
