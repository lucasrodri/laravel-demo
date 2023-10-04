import React from 'react';
import { Route, Routes } from 'react-router-dom';
import {
  APIStudentShowAll,
  APIStudentShowOne,
  APIStudentCreateEdit,
} from '../pages/APIStudentPage';

const StudentsRoutes = () => {
  //crie uma variavel chamada prefix e atribua ela ao path
  const prefix = '/students'
  return (
    <>
      <Route path={`${prefix}/`} element={<APIStudentShowAll />} />
      <Route path={`${prefix}/:id`} element={<APIStudentShowOne />} />
      <Route path={`${prefix}/:id/edit`} element={<APIStudentCreateEdit />} />
      <Route path={`${prefix}/create`} element={<APIStudentCreateEdit />} />
    </>
  );
};

export default StudentsRoutes;
