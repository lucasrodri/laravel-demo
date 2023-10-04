import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { ContactPage } from './pages/ContactPage';
import { APIDataPage } from './pages/APIDataPage';
import { NotFoundPage } from './pages/NotFoundPage';

import StudentsRoutes from './routes/StudentsRoutes'; 
import AdminsRoutes from './routes/AdminsRoutes'; 
import TrabsRoutes from './routes/TrabsRoutes';

export const AppRoutes = () => {
  const studentsRoutes = StudentsRoutes();
  const adminsRoutes = AdminsRoutes();
  const trabsRoutes = TrabsRoutes();
  return (
    <Router>
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">Laravel-Demo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item active">
              <Link to='/' className="nav-link" >Home <span class="sr-only">(current)</span></Link>
            </li>
            <li class="nav-item">
              <Link to='/students' className="nav-link" >Students API</Link>
            </li>
            <li class="nav-item">
              <Link to='/admins' className="nav-link" >Admin API</Link>
            </li>
            <li class="nav-item">
              <Link to='/trabs' className="nav-link" >Trab API</Link>
            </li>
          </ul>
        </div>
      </nav>
      <Routes>
        <Route path='/' element={<ContactPage />} />
        
        {/* Rotas da API */}
        {studentsRoutes}
        {adminsRoutes}
        {trabsRoutes}

        {/* Rota coringa para a página "Not Found" */}
        <Route path='*' element={<NotFoundPage />} />
      </Routes>
    </Router>
  );
};
