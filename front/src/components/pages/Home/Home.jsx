import MainLayout from '../../templates/MainLayout';
import { useAuth } from '../../../contexts/AuthContext';
import Header from "../../organisms/Header/Header";
import OurServices from "../../organisms/OurServices/OurServices";

const Home = () => {
    const { user } = useAuth();

    return (
        <MainLayout>
            <Header></Header>
            {console.log('User data:', user)}
            {user && (
                <p>
                    Votre rôle: {
                    user.roles && Object.values(user.roles).length > 0
                        ? Object.values(user.roles)[0]
                        : 'Aucun rôle'
                }
                </p>            )}
            <OurServices></OurServices>
        </MainLayout>
    );
};

export default Home;
