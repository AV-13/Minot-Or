import MainLayout from '../templates/MainLayout';
import { useAuth } from '../../contexts/AuthContext';

const Home = () => {
    const { user } = useAuth();

    return (
        <MainLayout>
            <h1>Bienvenue sur l'accueil</h1>
            {console.log('User data:', user)}
            {user && (
                <p>
                    Votre rôle : {
                    user.roles && Object.values(user.roles).length > 0
                        ? Object.values(user.roles)[0]
                        : 'Aucun rôle'
                }
                </p>            )}
        </MainLayout>
    );
};

export default Home;