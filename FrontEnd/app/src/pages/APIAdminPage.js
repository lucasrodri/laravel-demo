import React, { useState, useEffect } from 'react';
import api from '../api';
import { Link, useParams, useNavigate } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';

export const AdminDetailCard = ({ admin, showbuttons = true }) => {
  return (
    <div className="card">
      <div className="card-body">
        <h2 className="card-title">Detalhes do Admin {admin.id}</h2>
        <ul>
          <li><strong>ID:</strong> {admin.id}</li>
          <li><strong>Title:</strong> {admin.title}</li>
          <li><strong>Body:</strong> {admin.body}</li>
          <li><strong>Created At:</strong> {admin.created_at}</li>
          <li><strong>Updated At:</strong> {admin.updated_at}</li>
        </ul>
        {showbuttons ? (
          <div>
            <HandleEdit id={admin.id} />
            <HandleDelete id={admin.id} />
            <Link to={`/admins`} className='btn btn-secondary'>Voltar</Link>
          </div>
        ) : (
          <Link to={`/admins/${admin.id}`} className='btn btn-secondary'>Ver detalhes</Link>
        )}
      </div>
    </div>
  );
};


export const HandleEdit = ({ id }) => {
  const navigate = useNavigate();

  const handleEditClick = () => {
    navigate(`/admins/${id}/edit`);
  };

  return (
    <button className="btn btn-primary" onClick={handleEditClick}>Editar</button>
  );
};


export const HandleDelete = ({ id }) => {
  const navigate = useNavigate();

  const handleDeleteClick = async () => {
    try {
      await api.delete(`/admins/${id}`);
      navigate('/admins'); // Redirecionar de volta para a lista após a exclusão
    } catch (error) {
      console.error('Erro ao apagar estudante:', error);
    }
  };

  return (
    <button className="btn btn-danger" onClick={handleDeleteClick}>Apagar</button>
  );
};


// Componente reutilizável para obter a lista de estudantes
const AdminList = () => {
  const [admins, setAdmins] = useState([]);

  useEffect(() => {
    const fetchAdmins = async () => {
      try {
        const response = await api.get('/admins');
        setAdmins(response.data);
      } catch (error) {
        console.error('Erro ao buscar admins:', error);
      }
    };

    fetchAdmins();
  }, []);

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center">
        <h2>Lista de Admins Cadastrados na API</h2>
        <Link to={`/admins/create`} className='btn btn-primary ml-custom'>Criar Admin</Link>
      </div>
      {admins.map((admin) => (
        <div key={admin.id}>
          <AdminDetailCard
            admin={admin}
            showbuttons={false}
          />
        </div>
      ))}
    </div>
  );
};


export const APIAdminShowOne = () => {
  const [admin, setAdmin] = useState({});
  const { id } = useParams();

  useEffect(() => {
    const fetchAdmin = async () => {
      try {
        const response = await api.get(`/admins/${id}`);
        if (response.data.id) {
          setAdmin(response.data);
        } else {
          setAdmin({});
        }
      } catch (error) {
        console.error('Erro ao buscar estudante:', error);
      }
    };

    if (id) {
      fetchAdmin();
    }
  }, [id]);

  return (
    <div>
      {admin.id === undefined ? (
        <p>Admin não encontrado!</p>
      ) : (
        <AdminDetailCard
          admin={admin}
        />
      )}
    </div>
  );
};

export const APIAdminShowAll = () => {
  return (
    <div>
      <AdminList />
    </div>
  );
};

export const APIAdminCreateEdit = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [adminData, setAdminData] = useState({
    title: '',
    course: '',
  });

  // Função para buscar os dados do estudante a ser editado
  const fetchAdminData = async () => {
    try {
      const response = await api.get(`/admins/${id}`);
      if (response.status === 200) {
        if (response.data.id) {
          // Se o estudante existir, preencha os dados do estudante
          setAdminData(response.data);
        } else {
          // Se o estudante não existir, redirecione para a página 404
          navigate('/404'); // Redirecione para a rota de erro 404
        }
      } else {
        // Trate o erro aqui, como redirecionar para uma página de erro 404
        console('Erro ao buscar estudante:', response);
      }
    } catch (error) {
      // Trate o erro aqui, como redirecionar para uma página de erro
      console.error('Erro ao buscar estudante:', error);
    }
  };

  useEffect(() => {
    if (id) {
      fetchAdminData();
    }
  }, [id]);

  const handleSubmit = async (event) => {
    event.preventDefault();

    try {
      if (id) {
        // Se houver um ID, estamos editando, então usamos o método PUT
        await api.put(`/admins/${id}`, adminData);
        navigate(`/admins/${id}`); // Redirecionar para a página de visualização
      } else {
        // Caso contrário, estamos criando um novo estudante, usamos o método POST
        const response = await api.post('/admins', adminData);
        navigate(`/admins/${response.data.id}`); // Redirecionar para a página de visualização após a criação
      }

      // Redirecione ou faça algo após o sucesso
    } catch (error) {
      console.error('Erro ao buscar estudante:', error);
    }
  };

  return (
    <div>
      <h2>{id ? 'Editar Admin' : 'Criar Admin'}</h2>
      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label htmlFor="title" className="form-label">
            Nome:
          </label>
          <input
            type="text"
            className="form-control"
            id="title"
            name="title"
            value={adminData.title}
            onChange={(e) =>
              setAdminData({ ...adminData, title: e.target.value })
            }
            required
          />
        </div>
        <div className="mb-3">
          <label htmlFor="body" className="form-label">
            Curso:
          </label>
          <input
            type="text"
            className="form-control"
            id="body"
            name="body"
            value={adminData.body}
            onChange={(e) =>
              setAdminData({ ...adminData, body: e.target.value })
            }
            required
          />
        </div>
        <button type="submit" className="btn btn-primary">
          {id ? 'Editar' : 'Criar'}
        </button>
        <Link to={`/admins`} className='btn btn-secondary'>Voltar</Link>
      </form>
    </div>
  );
};

