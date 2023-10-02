php artisan migrate

# Iniciar o servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000 &

# Executar o consumidor de fila
php artisan mq:consume

# Mantenha o script em execução
tail -f /dev/null