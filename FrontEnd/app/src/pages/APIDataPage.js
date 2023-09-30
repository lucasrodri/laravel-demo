import React, { useState, useEffect } from "react";
import "../styles.css";
//import 'bootstrap/dist/css/bootstrap.min.css';
import { Card } from "react-bootstrap"; // Importe o componente de cartão do Bootstrap

export const APIDataPage = () => {
  const [dataAPI, setData] = useState([]);

  useEffect(() => {
    // fetch('/blog')
    // .then(response => {
    //   if (response.ok) {
    //     console.log('Conexão bem-sucedida');
    //     console.log(response.json());
    //   } else {
    //     console.error('Erro na conexão');
    //   }
    // })
    // .catch(error => {
    //   console.error('Erro na conexão:', error);
    // });
    fetch("/blog")
      .then(response => {
        if (!response.ok) {
          throw new Error("Erro na conexão");
        }
        return response.json();
      })
      .then(data => {
        setData(data); // Defina o estado com os dados retornados
      })
      .catch(error => {
        console.error('Erro na conexão:', error);
      });
  }, []);

  return (
    <div className="container">
      <h1>Posts do Blog</h1>
      <div className="row">
        {dataAPI.map(data => (
          <div className="col-md-4" key={data.id}>
            <Card className="custom-card">
              <Card.Body>
                <Card.Title className="custom-title">{data.title}</Card.Title>
                <Card.Text className="custom-text">{data.body}</Card.Text>
                <Card.Text>
                  <small className="text-muted">
                    Data de publicação: {data.created_at}
                  </small>
                </Card.Text>
              </Card.Body>
            </Card>
          </div>
        ))}
      </div>
    </div>
  );
};
