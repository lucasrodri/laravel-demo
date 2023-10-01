import React, { useState, useEffect } from 'react';
import api from '../api';
import { Link, useParams, useNavigate } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';

export const StudentDetailCard = ({ student, showbuttons = true }) => {
  return (
    <div className="card">
      <div className="card-body">
        <h2 className="card-title">Detalhes do Estudante {student.id}</h2>
        <ul>
          <li><strong>ID:</strong> {student.id}</li>
          <li><strong>Name:</strong> {student.name}</li>
          <li><strong>Course:</strong> {student.course}</li>
          <li><strong>Created At:</strong> {student.created_at}</li>
          <li><strong>Updated At:</strong> {student.updated_at}</li>
        </ul>
        {showbuttons ? (
          <div>
            <HandleEdit id={student.id} />
            <HandleDelete id={student.id} />
            <Link to={`/students`} className='btn btn-secondary'>Voltar</Link>
          </div>
        ) : (
          <Link to={`/students/${student.id}`} className='btn btn-secondary'>Ver detalhes</Link>
        )}
      </div>
    </div>
  );
};

export const HandleEdit = ({ id }) => {
  const navigate = useNavigate();

  const handleEditClick = () => {
    navigate(`/students/${id}/edit`);
  };

  return (
    <button className="btn btn-primary" onClick={handleEditClick}>Editar</button>
  );
};

export const HandleDelete = ({ id }) => {
  const navigate = useNavigate();

  const handleDeleteClick = async () => {
    try {
      await api.delete(`/students/${id}`);
      navigate('/students'); // Redirecionar de volta para a lista após a exclusão
    } catch (error) {
      console.error('Erro ao apagar estudante:', error);
    }
  };

  return (
    <button className="btn btn-danger" onClick={handleDeleteClick}>Apagar</button>
  );
};

// Componente reutilizável para obter a lista de estudantes
const StudentList = () => {
  const [students, setStudents] = useState([]);

  useEffect(() => {
    const fetchStudents = async () => {
      try {
        const response = await api.get('/students');
        setStudents(response.data);
      } catch (error) {
        console.error('Erro ao buscar estudantes:', error);
      }
    };

    fetchStudents();
  }, []);

  return (
    <div>
      <h2>Lista de Estudantes Cadastrados na API</h2>
      {students.map((student) => (
        <div key={student.id}>
          <StudentDetailCard
            student={student}
            showbuttons={false}
          />
        </div>
      ))}
    </div>
  );
};

export const APIStudentShowOne = () => {
  const [student, setStudent] = useState({});
  const { id } = useParams();

  useEffect(() => {
    const fetchStudent = async () => {
      try {
        const response = await api.get(`/students/${id}`);
        if (response.data.id) {
          setStudent(response.data);
        } else {
          setStudent({});
        }
      } catch (error) {
        console.error('Erro ao buscar estudante:', error);
      }
    };

    if (id) {
      fetchStudent();
    }
  }, [id]);

  return (
    <div>
      {student.id === undefined ? (
        <p>Estudante não encontrado!</p>
      ) : (
        <StudentDetailCard
          student={student}
        />
      )}
    </div>
  );
};

export const APIStudentShowAll = () => {
  return (
    <div>
      <StudentList />
    </div>
  );
};

export const APIStudentCreateEdit = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [studentData, setStudentData] = useState({
    name: '',
    course: '',
  });

  // Função para buscar os dados do estudante a ser editado
  const fetchStudentData = async () => {
    try {
      const response = await api.get(`/students/${id}`);
      if (response.status === 200) {
        if (response.data.id) {
          // Se o estudante existir, preencha os dados do estudante
          setStudentData(response.data);
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
      fetchStudentData();
    }
  }, [id]);

  const handleSubmit = async (event) => {
    event.preventDefault();

    try {
      if (id) {
        // Se houver um ID, estamos editando, então usamos o método PUT
        await api.put(`/students/${id}`, studentData);
        navigate(`/students/${id}`); // Redirecionar para a página de visualização
      } else {
        // Caso contrário, estamos criando um novo estudante, usamos o método POST
        const response = await api.post('/students', studentData);
        navigate(`/students/${response.data.id}`); // Redirecionar para a página de visualização após a criação
      }

      // Redirecione ou faça algo após o sucesso
    } catch (error) {
      console.error('Erro ao buscar estudante:', error);
    }
  };

  return (
    <div>
      <h2>{id ? 'Editar Estudante' : 'Criar Estudante'}</h2>
      <form onSubmit={handleSubmit}>
        <div className="mb-3">
          <label htmlFor="name" className="form-label">
            Nome:
          </label>
          <input
            type="text"
            className="form-control"
            id="name"
            name="name"
            value={studentData.name}
            onChange={(e) =>
              setStudentData({ ...studentData, name: e.target.value })
            }
            required
          />
        </div>
        <div className="mb-3">
          <label htmlFor="course" className="form-label">
            Curso:
          </label>
          <input
            type="text"
            className="form-control"
            id="course"
            name="course"
            value={studentData.course}
            onChange={(e) =>
              setStudentData({ ...studentData, course: e.target.value })
            }
            required
          />
        </div>
        <button type="submit" className="btn btn-primary">
          {id ? 'Editar' : 'Criar'}
        </button>
        <Link to={`/students`} className='btn btn-secondary'>Voltar</Link>
      </form>
    </div>
  );
};

