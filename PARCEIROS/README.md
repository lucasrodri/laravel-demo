# PARCEIROS 

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

Criando uma nova entidade com migration, model e controller:

```sh
php artisan make:model ParceiroTable
php artisan make:migration create_parceiro_tables_table
```

> Obs.: É obrigatório o uso do prefixo `create_` e sufixo `_table` no comando acima. Também deve-se seguir o padrão da model com `_` e um `s` no final

### Na migration adiciona as seguintes colunas:

- $table->text('title');  
- $table->text('body'); 

Na Model adiciona as seguintes colunas:

- protected $fillable = ['title', 'body'];

Volte no terminal do container e faça:

```sh
php artisan migrate
php artisan make:controller ParceiroTableController -m ParceiroTable
```

# Alterar os arquivos