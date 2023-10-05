# Exemplo de Aplicação Microserviços 
#### Laravel + React + Docker Compose + PostgreSQL + RabbitMQ

## Descrição do Projeto
<p>Este projeto é um exemplo de aplicação de microserviços utilizando Laravel, React, Docker Compose, PostgreSQL e RabbitMQ implementando uma arquitetura simples baseada em microserviços.</p>

## Arquitetura do Projeto

A imagem a seguir ilustra a arquitetura do projeto.

![Arquitetura do Projeto](ArquiteturaProgredir.png)

## Tecnologias Utilizadas
* [PHP](https://www.php.net/)
* [Laravel](https://laravel.com/)
* [PostgreSQL](https://www.postgresql.org/)
* [Docker](https://www.docker.com/)
* [Docker Compose](https://docs.docker.com/compose/)
* [React](https://pt-br.reactjs.org/)

## Instalação
### Pré-requisitos
* [Docker](https://www.docker.com/)
* [Docker Compose](https://docs.docker.com/compose/)
* [Git](https://git-scm.com/)

### Clonando o repositório
```bash
$ git clone https://github.com/lucasrodri/laravel-demo
```

### Configurando o ambiente
```bash
$ cd laravel-demo
$ cp .env.example .env
``` 

> Edite o arquivo `.env` e configure as variáveis de ambiente de acordo com o seu ambiente.

> Entre em cada pasta dos microserviços e siga as instruções de instalação do README.md de cada um.

### Iniciando o ambiente
```bash
$ docker-compose up -d --build
``` 

## Executando o ambiente
 Entre no navegador e acesse os seguintes  endereço:
 
 - [http://localhost:3000](http://localhost:3000) -> FrontEnd
 - [http://localhost:8000/swagger](http://localhost:8000/swagger) -> API
 - [http://localhost:15672](http://localhost:15672) -> RABBITMQ

## Equipe
* [Lucas Rodrigues Costa](mailto:lucasrodrigues@ibict.br)