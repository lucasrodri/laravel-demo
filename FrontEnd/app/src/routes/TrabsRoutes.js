import React from 'react';
import { Route, Routes } from 'react-router-dom';
import {
  APITrabShowAll,
  APITrabShowOne,
  APITrabCreateEdit,
} from '../pages/APITrabPage';

const TrabsRoutes = () => {
  //crie uma variavel chamada prefix e atribua ela ao path
  const prefix = '/trabs'
  return (
    <>
      <Route path={`${prefix}/`} element={<APITrabShowAll />} />
      <Route path={`${prefix}/:id`} element={<APITrabShowOne />} />
      <Route path={`${prefix}/:id/edit`} element={<APITrabCreateEdit />} />
      <Route path={`${prefix}/create`} element={<APITrabCreateEdit />} />
    </>
  );
};

export default TrabsRoutes;
