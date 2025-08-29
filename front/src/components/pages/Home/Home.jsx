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
            <Header></Header>
            <OurServices></OurServices>
            <CatchyFooter></CatchyFooter>
        </MainLayout>
    );
};

export default Home;
