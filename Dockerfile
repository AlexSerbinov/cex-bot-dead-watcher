FROM php:8.1-cli

# Install required dependencies and extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Create logs directory
RUN mkdir -p logs && chmod 777 logs

CMD ["php", "src/DeadWatcher.php"]