import Navbar from '../organisms/Navbar';

const MainLayout = ({ children }) => (
    <>
        <Navbar />
        <main>{children}</main>
    </>
);

export default MainLayout;
