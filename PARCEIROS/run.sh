# Install Composer dependencies
composer install --no-interaction

# Install Node.js dependencies (if you're using npm)
npm install

# Gere a chave APP_KEY
php artisan key:generate

# Execute outras configurações (como migrações de banco de dados, se necessário)
php artisan migrate

# Esperar até que o RabbitMQ esteja acessível
until curl -f http://rabbitmq:15672
do
  echo "Aguardando o RabbitMQ iniciar..."
  sleep 5
done

# Executar o consumidor de fila
php artisan mq:consume

# Mantenha o script em execução
tail -f /dev/null