import "./styles.css";
import { AppRoutes } from "./Routes";
import React, { useState } from 'react';

const FlashMessage = ({ message }) => {
  return <div className="flash-message">{message}</div>;
};

const App = () => {
  const [showMessage, setShowMessage] = useState(false);

  const handleShowMessage = () => {
    setShowMessage(true);

    // Define um temporizador para ocultar a mensagem após alguns segundos
    setTimeout(() => {
      setShowMessage(false);
    }, 3000); // Por exemplo, ocultar após 3 segundos
  };

  return (
    <>
      <AppRoutes />
    </>
  );
};

export default App;
