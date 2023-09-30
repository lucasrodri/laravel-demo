import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { ContactPage } from './pages/ContactPage';
import { APIDataPage } from './pages/APIDataPage';
import { NotFoundPage } from './pages/NotFoundPage';

export const AppRoutes = () => {
  return (
    <Router>
      <nav>
        <Link to='/'>Home</Link>
        <Link to='/pagina2'>Page 2</Link>
      </nav>
      <Routes>
        <Route path='/' element={<ContactPage />} />
        <Route path='/pagina2' element={<APIDataPage />} />
        {/* Rota coringa para a página "Not Found" */}
        <Route path='*' element={<NotFoundPage />} />
      </Routes>
    </Router>
  );
};
