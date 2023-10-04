import React from 'react';
import { Route, Routes } from 'react-router-dom';
import {
  APIAdminShowAll,
  APIAdminShowOne,
  APIAdminCreateEdit,
} from '../pages/APIAdminPage';

const AdminsRoutes = () => {
  //crie uma variavel chamada prefix e atribua ela ao path
  const prefix = '/admins'
  return (
    <>
      <Route path={`${prefix}/`} element={<APIAdminShowAll />} />
      <Route path={`${prefix}/:id`} element={<APIAdminShowOne />} />
      <Route path={`${prefix}/:id/edit`} element={<APIAdminCreateEdit />} />
      <Route path={`${prefix}/create`} element={<APIAdminCreateEdit />} />
    </>
  );
};

export default AdminsRoutes;
