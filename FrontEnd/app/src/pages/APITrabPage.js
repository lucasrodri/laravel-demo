import React, { useState, useEffect } from 'react';
import api from '../api';
import { Link, useParams, useNavigate } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';

export const TrabDetailCard = ({ trab, showbuttons = true }) => {
  return (
    <div className="card">
      <div className="card-body">
        <h2 className="card-title">Detalhes do Trab {trab.id}</h2>
        <ul>
          <li><strong>ID:</strong> {trab.id}</li>
          <li><strong>Title:</strong> {trab.title}</li>
          <li><strong>Body:</strong> {trab.body}</li>
          <li><strong>Created At:</strong> {trab.created_at}</li>
          <li><strong>Updated At:</strong> {trab.updated_at}</li>
        </ul>
        {showbuttons ? (
          <div>
            <HandleEdit id={trab.id} />
            <HandleDelete id={trab.id} />
            <Link to={`/trabs`} className='btn btn-secondary'>Voltar</Link>
          </div>
        ) : (
          <Link to={`/trabs/${trab.id}`} className='btn btn-secondary'>Ver detalhes</Link>
        )}
      </div>
    </div>
  );
};


export const HandleEdit = ({ id }) => {
  const navigate = useNavigate();

  const handleEditClick = () => {
    navigate(`/trabs/${id}/edit`);
  };

  return (
    <button className="btn btn-primary" onClick={handleEditClick}>Editar</button>
  );
};


export const HandleDelete = ({ id }) => {
  const navigate = useNavigate();

  const handleDeleteClick = async () => {
    try {
      await api.delete(`/trabs/${id}`);
      navigate('/trabs'); // Redirecionar de volta para a lista após a exclusão
    } catch (error) {
      console.error('Erro ao apagar estudante:', error);
    }
  };

  return (
    <button className="btn btn-danger" onClick={handleDeleteClick}>Apagar</button>
  );
};


// Componente reutilizável para obter a lista de estudantes
const TrabList = () => {
  const [trabs, setTrabs] = useState([]);

  useEffect(() => {
    const fetchTrabs = async () => {
      try {
        const response = await api.get('/trabs');
        setTrabs(response.data);
      } catch (error) {
        console.error('Erro ao buscar trabs:', error);
      }
    };

    fetchTrabs();
  }, []);

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center">
        <h2>Lista de Trabs Cadastrados na API</h2>
        <Link to={`/trabs/create`} className='btn btn-primary ml-custom'>Criar Trab</Link>
      </div>
      {trabs.map((trab) => (
        <div key={trab.id}>
          <TrabDetailCard
            trab={trab}
            showbuttons={false}
          />
        </div>
      ))}
    </div>
  );
};


export const APITrabShowOne = () => {
  const [trab, setTrab] = useState({});
  const { id } = useParams();

  useEffect(() => {
    const fetchTrab = async () => {
      try {
        const response = await api.get(`/trabs/${id}`);
        if (response.data.id) {
          setTrab(response.data);
        } else {
          setTrab({});
        }
      } catch (error) {
        console.error('Erro ao buscar estudante:', error);
      }
    };

    if (id) {
      fetchTrab();
    }
  }, [id]);

  return (
    <div>
      {trab.id === undefined ? (
        <p>Trab não encontrado!</p>
      ) : (
        <TrabDetailCard
          trab={trab}
        />
      )}
    </div>
  );
};

export const APITrabShowAll = () => {
  return (
    <div>
      <TrabList />
    </div>
  );
};

export const APITrabCreateEdit = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [trabData, setTrabData] = useState({
    title: '',
    course: '',
  });

  // Função para buscar os dados do estudante a ser editado
  const fetchTrabData = async () => {
    try {
      const response = await api.get(`/trabs/${id}`);
      if (response.status === 200) {
        if (response.data.id) {
          // Se o estudante existir, preencha os dados do estudante
          setTrabData(response.data);
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
      fetchTrabData();
    }
  }, [id]);

  const handleSubmit = async (event) => {
    event.preventDefault();

    try {
      if (id) {
        // Se houver um ID, estamos editando, então usamos o método PUT
        await api.put(`/trabs/${id}`, trabData);
        navigate(`/trabs/${id}`); // Redirecionar para a página de visualização
      } else {
        // Caso contrário, estamos criando um novo estudante, usamos o método POST
        const response = await api.post('/trabs', trabData);
        navigate(`/trabs/${response.data.id}`); // Redirecionar para a página de visualização após a criação
      }

      // Redirecione ou faça algo após o sucesso
    } catch (error) {
      console.error('Erro ao buscar estudante:', error);
    }
  };

  return (
    <div>
      <h2>{id ? 'Editar Trab' : 'Criar Trab'}</h2>
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
            value={trabData.title}
            onChange={(e) =>
              setTrabData({ ...trabData, title: e.target.value })
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
            value={trabData.body}
            onChange={(e) =>
              setTrabData({ ...trabData, body: e.target.value })
            }
            required
          />
        </div>
        <button type="submit" className="btn btn-primary">
          {id ? 'Editar' : 'Criar'}
        </button>
        <Link to={`/trabs`} className='btn btn-secondary'>Voltar</Link>
      </form>
    </div>
  );
};

