# Install Composer dependencies
composer install --no-interaction

# Install Node.js dependencies (if you're using npm)
npm install

# Gere a chave APP_KEY
php artisan key:generate

# Execute outras configurações (como migrações de banco de dados, se necessário)
php artisan migrate

# Script para gerar a documentação da API
cd development/
./swagger.sh 
cd ..

# Iniciar o servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000