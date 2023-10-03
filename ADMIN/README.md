# ADMIN 

Defina as senhas no .env da sua aplicação manualmente. 

```sh
nano .env
```

> Devem ser as mesmas senhas do `docker-compose.yml` do projeto base!

Crie o arquivo de env da sua aplicação e definas as senhas:

```sh
source .env
cp app/.env.example app/.env
sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" app/.env
sed -i "s/MQ_PASS=/MQ_PASS=\"$MQ_PASS\"/" app/.env
```