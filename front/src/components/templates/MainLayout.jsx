import Navbar from '../organisms/Navbar/Navbar';
import Footer from "../organisms/Footer/Footer";

const MainLayout = ({ children }) => (
    <>
        <Navbar />
        <main>{children}</main>
        <Footer></Footer>
    </>
);

export default MainLayout;
