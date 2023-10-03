FROM php:8.2-fpm AS base

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    postgresql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Install Node.js
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && apt-get install -y npm

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#Copy the run.sh file
COPY run.sh /usr/bin/run.sh

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user && \
    chmod +x /usr/bin/run.sh

# Install PHP Sockets para funcionamiento do rabbitmq
RUN docker-php-ext-install sockets

# Set working directory
WORKDIR /var/www

USER $user

EXPOSE 8000

CMD ["/usr/bin/run.sh"]