import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';
import { ContactPage } from './pages/ContactPage';
import { APIDataPage } from './pages/APIDataPage';
import { APIStudentShowAll, APIStudentShowOne, APIStudentCreateEdit } from './pages/APIStudentPage';
import { APIAdminShowAll, APIAdminShowOne, APIAdminCreateEdit } from './pages/APIAdminPage';
import { NotFoundPage } from './pages/NotFoundPage';

export const AppRoutes = () => {
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
              <Link to='/pagina2' className="nav-link" >Page 2</Link>
            </li>
            <li class="nav-item">
              <Link to='/students' className="nav-link" >Students API</Link>
            </li>
            <li class="nav-item">
              <Link to='/admins' className="nav-link" >Admin API</Link>
            </li>
          </ul>
        </div>
      </nav>
      <Routes>
        <Route path='/' element={<ContactPage />} />
        <Route path='/pagina2' element={<APIDataPage />} />
        {/* Rotas da API students */}
        <Route path='/students' element={<APIStudentShowAll />} />
        <Route path='/students/:id' element={<APIStudentShowOne />} />
        <Route path='/students/:id/edit' element={<APIStudentCreateEdit />} />
        <Route path='/students/create' element={<APIStudentCreateEdit />} />
        {/* Rotas da API admins */}
        <Route path='/admins' element={<APIAdminShowAll />} />
        <Route path='/admins/:id' element={<APIAdminShowOne />} />
        <Route path='/admins/:id/edit' element={<APIAdminCreateEdit />} />
        <Route path='/admins/create' element={<APIAdminCreateEdit />} />
        {/* Rota coringa para a página "Not Found" */}
        <Route path='*' element={<NotFoundPage />} />
      </Routes>
    </Router>
  );
};
